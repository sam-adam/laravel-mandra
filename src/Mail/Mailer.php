<?php

namespace Mandra\Mail;

use Swift_Mailer;
use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Mail\Mailer as BaseMailer;
use Mandra\Decorators\Decorator as DecoratorInterface;

/**
 * Class Mailer
 *
 * @package Mandra\Mail
 */
class Mailer extends BaseMailer
{
    /** @var MailerContract */
    protected $mailer;
    /** @var DecoratorInterface[] */
    protected $decorators;
    /** @var string */
    protected $originalBody;

    public function __construct(Factory $views, Swift_Mailer $swift, Dispatcher $events = null)
    {
        parent::__construct($views, $swift, $events);
    }

    /** {@inheritDoc} */
    protected function addContent($message, $view, $plain, $raw, $data)
    {
        parent::addContent($message, $view, $plain, $raw, $data);

        $this->originalBody = $message->getSwiftMessage()->getBody();

        foreach ($this->decorators as $decorator) {
            $decorator->decorate($message, $data);
        }
    }
}