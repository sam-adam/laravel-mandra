<?php

namespace LaravelMandra\Decorators;

use LaravelMandra\Helper\UrlBuilder;
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
        $loggedParams = config('mandra.pixelTracker.dataKeys');
        $body         = $message->getSwiftMessage()->getBody();
        $url          = UrlBuilder::buildUrl(url(config('mandra.pixelTracker.url')), $data + ['param' => '1'], array_merge($loggedParams, ['param']));

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