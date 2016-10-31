<?php

namespace LaravelMandra\Decorators;

use LaravelMandra\Helper\MessageHelper;
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
        $campaignParams = MessageHelper::buildCampaignParams($message);
        $body           = $message->getSwiftMessage()->getBody();

        if (isset($data['utms'])) {
            $campaignParams = array_merge($data['utms'], $campaignParams);
        }

        foreach (["'", '"'] as $quote) {
            $body = preg_replace_callback("/href=\\{$quote}([^\\{$quote}]*)\\{$quote}/", function ($matches) use ($trackerLink, $trackerLinkKey, $quote, $campaignParams) {
                $url = MessageHelper::buildUrl($matches[1], $campaignParams);

                return "href={$quote}".strtr($trackerLink, [$trackerLinkKey => rawurlencode($url)])."{$quote}";
            }, $body);
        }

        $message->getSwiftMessage()->setBody($body);

        return $message;
    }
}