<?php

namespace LaravelMandra\Decorators;

use LaravelMandra\Mail\Message;

/**
 * Interface Decorator
 *
 * @package Mandra\Decorators
 */
interface Decorator
{
    /**
     * Decorate an instance of swift message
     *
     * @param Message $message
     * @param array   $data
     *
     * @return Message
     */
    public function decorate(Message $message, $data);
}