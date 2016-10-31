<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Decorator settings
     |--------------------------------------------------------------------------
     |
     | Decorators will be passed a Message and an array of data, and will have chance to
     | tinker with message's content before it sent
     |
     */
    'decorators'   => [
        \LaravelMandra\Decorators\LinkTracker::class,
        \LaravelMandra\Decorators\PixelTracker::class
    ],

    /*
     |--------------------------------------------------------------------------
     | Default decorators settings
     |--------------------------------------------------------------------------
     |
     | -. clickTracker
     |    Will replace all element's 'href' value with clickTracker.link value (clickTracker.link should contain clickTracker.key as placeholder), example:
     |      'clickTracker' => [
     |           'link' => 'http://www.clickTracker.com/originalUrl=:originalLink?id=:messageId'
     |       ],
     |       ...
     |
     |    Available placeholders:
     |      ':originalLink':   The original url
     |      ':messageTo':      Comma separated message recipients
     |      ':messageId':      Message unique id
     |      ':messageSubject': Message subject
     |      ':messageType':    Message type
     |
     | -. pixelTracker
     |    Appends a <img> element with src defined in pixelTracker.url
     |
     */
    'clickTracker' => [
        'link' => ''
    ],
    'pixelTracker' => [
        'url'      => ''
    ],

    /*
     |--------------------------------------------------------------------------
     | Logging settings
     |--------------------------------------------------------------------------
     |
     | Define logging behavior for mandra, available options:
     |
     | -. doLog:        Global options to set if a mail sending should be logged at all
     | -. logContent:   Defines should the message content be shipped after sending event
     | -. disk:         Disk to use while shipping mesage content, corresponds to 'filesystem' config (should be located at config/filesystem.php)
     | -. writes:       Log writers, must implements \LaravelMandra\Mail\Log\LogWriter interface
     |
     */
    'logging'      => [
        'doLog'      => true,
        'logContent' => true,
        'disk'       => 'mandra',
        'writers'    => [
            \LaravelMandra\Mail\Log\Writers\ApplicationWriter::class
        ]
    ],

    /*
     |--------------------------------------------------------------------------
     | Misc settings
     |--------------------------------------------------------------------------
     |
     | -. allowController:  Allow mandra's controllers and routes to be registered
     |
     */
    'allowController' => false
];