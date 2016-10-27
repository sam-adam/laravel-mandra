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
        if ($this->doLogContent) {
            $this->logContent($message);
        }

        foreach ($this->logWriters as $logWriter) {
            $logWriter->writeLog($message, $mailer, $result, $messageData);
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
}