<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Anime Streaming Sources
    |--------------------------------------------------------------------------
    |
    | Format:
    | anime_id => [
    |     episode_id => [
    |         'url' => 'licensed video URL',
    |         'type' => 'application/x-mpegURL',
    |     ],
    | ],
    |
    | Add only video URLs you are authorized to use.
    |
    */

    'sources' => [

        /*
        Example structure (not a real streaming URL):

        '1' => [
            '1' => [
                'url' => env('ANIME_1_EPISODE_1_URL'),
                'type' => 'application/x-mpegURL',
            ],
        ],

        */

    ],

];
