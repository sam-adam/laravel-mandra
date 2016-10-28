<?php

return [
    'decorators'   => [
        \LaravelMandra\Decorators\LinkTracker::class,
        \LaravelMandra\Decorators\PixelTracker::class
    ],
    'clickTracker' => [
        'link' => '',
        'key'  => ':originalLink'
    ],
    'pixelTracker' => [
        'url'      => '',
        'dataKeys' => [
            'utmString'
        ]
    ],
    'logging'      => [
        'doLog'      => true,
        'logContent' => true,
        'disk'       => 'mandra',
        'writers'    => [
            \LaravelMandra\Mail\Log\Writers\ApplicationWriter::class
        ]
    ]
];