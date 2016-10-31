<?php

namespace LaravelMandra\Decorators;

use LaravelMandra\Helper\MessageHelper;
use LaravelMandra\Mail\Message;

/**
 * Class PixelTracker
 *
 * @package Mandra\Decorators
 */
class PixelTracker implements Decorator
{
    /** {@inheritDoc} */
    public function decorate(Message $message, $data)
    {
        $body           = $message->getSwiftMessage()->getBody();
        $campaignParams = MessageHelper::buildCampaignParams($message);

        if (isset($data['utms'])) {
            $campaignParams = array_merge($data['utms'], $campaignParams);
        }

        $url = MessageHelper::buildUrl(url(config('mandra.pixelTracker.url')), $campaignParams);
        $img = "<img src='{$url}' id='pixtrck' />";

        if (strpos($body, '</body>') !== false) {
            $body = str_replace('</body>', $img.'</body>', $body);
        } elseif (preg_match('/<\/[a-zA-Z+]+>/', $body)) {
            $body .= $img;
        }

        $message->getSwiftMessage()->setBody($body);

        return $message;
    }
}