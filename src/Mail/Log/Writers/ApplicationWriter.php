<?php

namespace Mandra\Mail\Log\Writers;

use Illuminate\Support\Facades\Log;
use Mandra\Mail\Log\LogWriter;
use Mandra\Mail\Mailer;
use Mandra\Mail\Message;

/**
 * Class ApplicationWriter
 *
 * @package Mandra\Mail\Log\Writers
 */
class ApplicationWriter implements LogWriter
{
    /** {@inheritDoc} */
    public function writeLog(Message $message, Mailer $mailer, $result, array $messageData = [])
    {
        $logContent = '[MailSend][Mailer: :mailer][Subject: :subject][Result: success][Recipients: :recipients][CC: :ccs]';

        Log::info(strtr($logContent, [
            ':mailer'     => $this->getMailerString($mailer),
            ':subject'    => $message->getSwiftMessage()->getSubject(),
            ':recipients' => json_encode($message->getSwiftMessage()->getTo()),
            ':ccs'        => json_encode($message->getSwiftMessage()->getCc())
        ]));
    }

    /**
     * @param \Mandra\Mail\Mailer $mailer
     *
     * @return array
     */
    protected function getMailerString(Mailer $mailer)
    {
        return json_encode([
            'class'     => get_class($mailer),
            'transport' => get_class($mailer->getSwiftMailer()->getTransport())
        ]);
    }
}