<?php

namespace LaravelMandra\Mail;

use Illuminate\Contracts\Mail\Mailable;
use Swift_Mailer;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Mail\Mailer as BaseMailer;
use LaravelMandra\Decorators\Decorator as DecoratorInterface;
use Swift_Message;

/**
 * Class Mailer
 *
 * @package Mandra\Mail
 */
class Mailer extends BaseMailer
{
    /** @var DecoratorInterface[] */
    protected $decorators;
    /** @var string */
    protected $originalBody;

    public function __construct(Factory $views, Swift_Mailer $swift, Dispatcher $events = null)
    {
        parent::__construct($views, $swift, $events);
    }

    /** {@inheritDoc} */
    protected function createMessage()
    {
        $message = new Message(new Swift_Message);

        if (!empty($this->from['address'])) {
            $message->from($this->from['address'], $this->from['name']);
        }

        return $message;
    }

    /** {@inheritDoc} */
    public function send($view, array $data = [], $callback = null)
    {
        if ($view instanceof MailableContract) {
            $view->send($this);
        }

        list($view, $plain, $raw) = $this->parseView($view);

        $data['message'] = $message = $this->createMessage();

        $this->addContent($message, $view, $plain, $raw, $data);
        $this->callMessageBuilder($callback, $message);

        if (isset($this->to['address'])) {
            $message->to($this->to['address'], $this->to['name'], true);
        }

        try {
            $this->sendSwiftMessage($message->getSwiftMessage());

            if ($this->events) {
                $this->events->fire(new Events\MessageSent($message, $this, $data));
            }
        } catch (\Exception $ex) {
            if ($this->events) {
                $this->events->fire(new Events\MessageSendingFailed($message, $this, $data));
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param Message $message
     */
    protected function addContent($message, $view, $plain, $raw, $data)
    {
        parent::addContent($message, $view, $plain, $raw, $data);

        $message->setKey(str_replace('/', '.', $view));

        $this->originalBody = $message->getSwiftMessage()->getBody();

        foreach ($this->decorators as $decorator) {
            $decorator->decorate($message, $data);
        }
    }
}