<?php

namespace LaravelMandra\Mail\Events;

use Illuminate\Queue\SerializesModels;
use LaravelMandra\Mail\Mailer;
use LaravelMandra\Mail\Message;

/**
 * Class MessageSendingFailed
 *
 * @package Mandra\Mail\Events
 */
class MessageSendingFailed
{
    use SerializesModels;

    /** @var Message */
    protected $message;
    /** @var Mailer */
    protected $mailer;
    /** @var array */
    protected $messageData;
    /** @var \Exception */
    protected $exception;

    public function __construct(Message $message, Mailer $mailer, array $messageData = [], \Exception $exception = null)
    {
        $this->message     = $message;
        $this->mailer      = $mailer;
        $this->messageData = $messageData;
        $this->exception   = $exception;
    }

    /** @return \Exception */
    public function getException()
    {
        return $this->exception;
    }

    /** @return Mailer */
    public function getMailer()
    {
        return $this->mailer;
    }

    /** @return Message */
    public function getMessage()
    {
        return $this->message;
    }

    /** @return array */
    public function getMessageData()
    {
        return $this->messageData;
    }
}
