<?php

namespace LaravelMandra\Mail;

use Illuminate\Mail\Mailable;

/**
 * Class Mail
 *
 * @package App\Mail
 */
abstract class Mail extends Mailable
{
    public function __construct() { }

    /** @return string */
    public function getSubject()
    {
        return $this->subject;
    }

    /** @return array */
    public function getTo()
    {
        return $this->to;
    }

    /** @return array */
    public function getCC()
    {
        return $this->cc;
    }
}