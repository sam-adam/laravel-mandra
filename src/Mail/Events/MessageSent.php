<?php

namespace LaravelMandra\Mail\Events;

use Illuminate\Queue\SerializesModels;
use LaravelMandra\Mail\Mailer;
use LaravelMandra\Mail\Message;

/**
 * Class MessageSent
 *
 * @package Mandra\Mail\Events
 */
class MessageSent
{
    use SerializesModels;

    /** @var Message */
    protected $message;
    /** @var Mailer */
    protected $mailer;
    /** @var array */
    protected $messageData;

    public function __construct(Message $message, Mailer $mailer, array $messageData = [])
    {
        $this->message     = $message;
        $this->mailer      = $mailer;
        $this->messageData = $messageData;
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
