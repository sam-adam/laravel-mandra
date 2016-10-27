<?php

namespace Mandra\Mail;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Class Logger
 *
 * @package Mandra\Mail
 */
class Logger
{
    /** @var callable */
    protected $logCallback;
    /** @var Filesystem */
    protected $filesystem;
    /** @var boolean */
    protected $doLogContent;

    public function __construct(callable $logCallback, Filesystem $filesystem = null, $doLogContent = false)
    {
        $this->filesystem   = $filesystem;
        $this->doLogContent = $doLogContent;
    }

    public function log(BaseMail $mail)
    {
        call_user_func_array($this->logCallback, []);

        if ($this->doLogContent) {
            $this->logContent($mail);
        }
    }

    protected function logContent(BaseMail $mail)
    {
        $messageId     = $mail->getMessageId();
        $dateNamespace = Carbon::now()->format('/Y/m/d');
        $filename      = $messageId;
        $html          = gzcompress($mail->getBody());
        $stream        = fopen('php://memory', 'r+');

        fwrite($stream, $html);
        rewind($stream);

        $this->filesystem->put($dateNamespace.'/'.$filename, $stream);
    }
}