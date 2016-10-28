<?php

namespace LaravelMandra\Decorators;

use LaravelMandra\Mail\Message;

/**
 * Class LinkTracker
 *
 * @package Mandra\Decorators
 */
class LinkTracker implements Decorator
{
    /** {@inheritDoc} */
    public function decorate(Message $message, $data)
    {
        $trackerLink    = config('mandra.clickTracker.link');
        $trackerLinkKey = config('mandra.clickTracker.key');
        $body           = $message->getSwiftMessage()->getBody();

        foreach (["'", '"'] as $quote) {
            $body = preg_replace_callback("/href=\\{$quote}([^\\{$quote}]*)\\{$quote}/", function ($matches) use ($trackerLink, $trackerLinkKey, $quote) {
                return "href={$quote}".strtr($trackerLink, [$trackerLinkKey => rawurlencode($matches[1])])."{$quote}";
            }, $body);
        }

        $message->getSwiftMessage()->setBody($body);

        return $message;
    }
}