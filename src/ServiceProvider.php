<?php

namespace LaravelMandra;

use Illuminate\Log\Writer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\MailServiceProvider as BaseServiceProvider;
use LaravelMandra\Decorators\Decorator;
use LaravelMandra\Mail\Events\MessageSendingFailed;
use LaravelMandra\Mail\Events\MessageSent;
use LaravelMandra\Mail\Log\Logger;
use LaravelMandra\Mail\Mailer;

/**
 * Class ServiceProvider
 *
 * @package Mandra
 */
class ServiceProvider extends BaseServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSwiftMailer();

        // register mail content decorators
        $this->app->bind('mandra.decorators', function ($app) {
            $decorators = [];

            /** @var Decorator $decorator */
            foreach ($app['config']['mandra.decorators'] as $decorator) {
                $interfaces = class_implements($decorator);

                if (is_array($interfaces) && in_array(Decorator::class, $interfaces)) {
                    array_push($decorators, $decorator);
                }
            }

            return $decorators;
        });

        // register mail log writers
        $this->app->bind('mandra.log.writers', function ($app) {
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
            if ($disk = $app['config']['mandra.logging.disk']) {
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
                $app['mandra.log.writers'],
                $app['mandra.logger.filesystem'],
                $app['config']['mandra.logging.logContent']
            );
        });

        // override mailer
        $this->app->singleton('mailer', function ($app) {
            $mailer = new Mailer(
                $app['view'],
                $app['swift.mailer'],
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

            foreach ($app['mandra.decorators'] as $decorator) {
                $mailer->addDecorator($app->make($decorator));
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

        if (config('mandra.allowController')) {
            if (!$this->app->routesAreCached()) {
                require __DIR__.'/../routes.php';
            }

            $this->loadViewsFrom(__DIR__.'/../resources/views', 'mandra');
        }

        if ($this->app['config']['mandra.logging.doLog']) {
            Event::listen(MessageSent::class, function (MessageSent $event) {
                /** @var Logger $logger */
                $logger = $this->app['mandra.logger'];

                $logger->log($event->getMessage(), $event->getMailer(), true, $event->getMessageData());
            });

            Event::listen(MessageSendingFailed::class, function (MessageSendingFailed $event) {
                /** @var Logger $logger */
                $logger = $this->app['mandra.logger'];

                $logger->log($event->getMessage(), $event->getMailer(), false, $event->getMessageData());
            });
        }
    }

    /** {@inheritDoc} */
    public function provides()
    {
        return ['mailer', 'mandra.decorators', 'mandra.log.writers', 'mandra.logger.filesystem', 'mandra.logger'];
    }
}