<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media / Image Processing
    |--------------------------------------------------------------------------
    */
    'media' => [
        'editor_path' => '/images/editor',
        'resize_url' => env('MIX_IMAGE_RESIZE_URL'),
        'transparent_image' => env('MIX_TRANSPARENT_IMAGE'),
        'web_fonts_api_key' => env('MIX_PERXI_WEB_FONTS_API'),
    ],

];
