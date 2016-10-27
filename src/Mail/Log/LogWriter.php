<?php

namespace Mandra\Mail\Log;

use Mandra\Mail\Mailer;
use Mandra\Mail\Message;

/**
 * Interface LogWriter
 *
 * @package Mandra\Mail\Log
 */
interface LogWriter
{
    /**
     * Write log about mail sending
     *
     * @param Message $message
     * @param Mailer  $mailer
     * @param boolean $result
     * @param array   $messageData
     *
     * @return void
     */
    public function writeLog(Message $message, Mailer $mailer, $result, array $messageData = []);
}