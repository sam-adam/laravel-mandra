<?php

namespace LaravelMandra\Mail;

use Illuminate\Mail\Message as BaseMessage;

/**
 * Class Message
 *
 * @package Mandra\Mail
 */
class Message extends BaseMessage
{
    /** @var string */
    protected $id;
    /** @var string */
    protected $key;

    public function __construct(\Swift_Message $swift)
    {
        parent::__construct($swift);

        $this->id = date('YmdHis').uniqid('_');
    }

    /**
     * Get the unique id of this message
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get message key, eg: email_registration_success
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the message key
     *
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }
}
