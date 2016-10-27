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
        'disk'       => 'mandra'
    ]
];