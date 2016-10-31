<?php

namespace LaravelMandra\Mail\Log;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use LaravelMandra\Mail\Mailer;
use LaravelMandra\Mail\Message;

/**
 * Class Logger
 *
 * @package Mandra\Mail
 */
class Logger
{
    /** @var LogWriter[] */
    protected $logWriters;
    /** @var Filesystem */
    protected $filesystem;
    /** @var boolean */
    protected $doLogContent;

    public function __construct(array $logWriters = [], Filesystem $filesystem = null, $doLogContent = false)
    {
        $this->logWriters   = $logWriters;
        $this->filesystem   = $filesystem;
        $this->doLogContent = $doLogContent;
    }

    /**
     * Do logging for email sending
     *
     * @param Message $message
     * @param Mailer  $mailer
     * @param boolean $result
     * @param array   $messageData
     *
     * @return void
     */
    public function log(Message $message, Mailer $mailer, $result, array $messageData = [])
    {
        $messageMeta = $this->buildMessageMeta($message);

        if (isset($message['logData'])) {
            $messageMeta = array_merge($messageMeta['logData'], $messageMeta);
        }

        if ($this->doLogContent && $this->filesystem) {
            $this->logContent($message);
        }

        foreach ($this->logWriters as $logWriter) {
            $logWriter->writeLog($message, $mailer, $result, $messageMeta);
        }
    }

    /**
     * Log a mail content to a filesystem
     *
     * @param Message $message
     *
     * @return void
     */
    protected function logContent(Message $message)
    {
        $messageId     = $message->getId();
        $dateNamespace = Carbon::now()->format('/Y/m/d');
        $filename      = $messageId;
        $html          = gzcompress($message->getSwiftMessage()->getBody());
        $stream        = fopen('php://memory', 'r+');

        fwrite($stream, $html);
        rewind($stream);

        $this->filesystem->put($dateNamespace.'/'.$filename, $stream);

        fclose($stream);
    }

    /**
     * Build message meta for logging
     *
     * @param Message $message
     *
     * @return array
     */
    protected function buildMessageMeta(Message $message)
    {
        return [
            'mail_type'     => $message->getKey(),
            'project'       => config('app.name'),
            'is_production' => app()->environment() == 'production' ? 1 : 0,
            'is_loggable'   => true,
            'to'            => $message->getSwiftMessage()->getTo(),
            'subject'       => $message->getSwiftMessage()->getSubject(),
            'cc'            => $message->getSwiftMessage()->getCc(),
            'reply_to'      => $message->getSwiftMessage()->getReplyTo()
        ];
    }
}