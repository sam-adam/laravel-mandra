<?php

namespace Mandra\Decorators;

use Mandra\Mail\Message;

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
            $body = preg_replace_callback("/href=\\{$quote}(.*)\\{$quote}/", function ($matches) use ($trackerLink, $trackerLinkKey) {
                return strtr($trackerLink, [$trackerLinkKey => rawurlencode($matches[1])]);
            }, $body);
        }

        $message->getSwiftMessage()->setBody($body);

        return $message;
    }
}