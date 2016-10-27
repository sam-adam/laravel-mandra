<?php

namespace Mandra\Mail\Events;

use Illuminate\Queue\SerializesModels;
use Mandra\Mail\Mailer;
use Mandra\Mail\Message;

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
