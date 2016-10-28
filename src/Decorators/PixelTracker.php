<?php

namespace LaravelMandra\Decorators;

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
        $url          = url(config('mandra.pixelTracker.url'));
        $params       = 'param=1';

        foreach ($loggedParams as $loggedParam) {
            if (isset($data[$loggedParam]) && is_string($data[$loggedParam]) || is_numeric($data[$loggedParam])) {
                $params .= ('&'.trim($data[$loggedParam], '&\t\n\r'));
            }
        }

        $url = $url.'?'.$params;
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