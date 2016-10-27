<?php

return [
    'decorators'   => [
        \Mandra\Decorators\LinkTracker::class,
        \Mandra\Decorators\PixelTracker::class
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
            \Mandra\Mail\Log\Writers\ApplicationWriter::class
        ]
    ]
];