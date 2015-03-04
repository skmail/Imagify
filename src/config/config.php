<?php

return  [

    'base_route' => 'cachedImages',
    'route' => '{method}/{width}/{height}/{source}',

    'quality' => 80,

    'max' => [
        'width' => 0,
        'height' => 0
    ],

    'min' => [
        'width' => 10,
        'height' => 10
    ],
    'watermark' => null
];