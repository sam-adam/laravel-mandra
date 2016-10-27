<?php

namespace Mandra;

use Illuminate\Log\Writer;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Mandra\Decorators\Decorator;
use Mandra\Mail\Events\MessageSent;
use Mandra\Mail\Log\Logger;

/**
 * Class ServiceProvider
 *
 * @package Mandra
 */
class ServiceProvider extends BaseServiceProvider
{
    /** @var Mailer */
    protected $originalMailer;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // register mail content decorators
        $this->app->bindIf('mandra.decorators', function ($app) {
            $decorators = [];

            /** @var Decorator $decorator */
            foreach ($app['config']['mandra.decorators'] as $decorator) {
                $interfaces = class_implements($decorator);

                if (is_array($interfaces) && in_array(Decorator::class, $interfaces)) {
                    array_push($decorators, $app->make($decorator));
                }
            }

            return $decorators;
        });

        // register mail log writers
        $this->app->bindIf('mandra.log.writers', function ($app) {
            $writers = [];

            /** @var Writer $writer */
            foreach ($app['config']['mandra.logging.writers'] as $writer) {
                $interfaces = class_implements($writer);

                if (is_array($interfaces) && in_array(Writer::class, $interfaces)) {
                    array_push($writers, $app->make($writer));
                }
            }

            return $writers;
        });

        // register filesystem implementation
        $this->app->singleton('mandra.logger.filesystem', function ($app) {
            if ($disk = $app['config']['mandra.logger.disk']) {
                $filesystem = Storage::disk($disk);

                if ($filesystem) {
                    return $filesystem;
                }
            }

            return null;
        });

        // register the logger
        $this->app->singleton('mandra.logger', function ($app) {
            return new Logger(
                $app['config']['mandra.logging.writers'],
                $app['mandra.logger.filesystem'],
                $app['config']['mandra.logging.logContent']
            );
        });

        // override mailer
        $this->app->singleton('mailer', function ($app) {
            $mailer = new Mailer(
                $this->originalMailer->getViewFactory(),
                $this->originalMailer->getSwiftMailer(),
                $app['events']
            );
            $mailer->setContainer($app);

            if ($app->bound('queue')) {
                $mailer->setQueue($app['queue']);
            }


            $from = $app['config']['mail.from'];

            if (is_array($from) && isset($from['address'])) {
                $mailer->alwaysFrom($from['address'], $from['name']);
            }

            $to = $app['config']['mail.to'];

            if (is_array($to) && isset($to['address'])) {
                $mailer->alwaysTo($to['address'], $to['name']);
            }

            return $mailer;
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/mandra.php' => config_path('mandra.php')
        ]);

        $this->originalMailer = $this->app['mailer'];

        if ($this->app['config']['mandra.doLog']) {
            Event::listen(MessageSent::class, function (MessageSent $event) {
                /** @var Logger $logger */
                $logger = $this->app['mandra.logger'];

                $logger->log($event->getMessage(), $event->getMailer(), true, $event->getMessageData());
            });
        }
    }
}