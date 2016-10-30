<?php

namespace LaravelMandra\Decorators;

use LaravelMandra\Helper\UrlBuilder;
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
        $loggedParams   = config('mandra.clickTracker.dataKeys');
        $body           = $message->getSwiftMessage()->getBody();

        foreach (["'", '"'] as $quote) {
            $body = preg_replace_callback("/href=\\{$quote}([^\\{$quote}]*)\\{$quote}/", function ($matches) use ($trackerLink, $trackerLinkKey, $quote, $loggedParams, $data) {
                $url = UrlBuilder::buildUrl($matches[1], $data, $loggedParams);

                return "href={$quote}".strtr($trackerLink, [$trackerLinkKey => rawurlencode($url)])."{$quote}";
            }, $body);
        }

        $message->getSwiftMessage()->setBody($body);

        return $message;
    }
}