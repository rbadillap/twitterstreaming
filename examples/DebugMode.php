<?php
// $ php DebugMode.php

require_once 'autoload.php';

use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints;

(new Tracker)
    ->endpoint(Endpoints\PublicEndpoint::class, 'filter')
    ->parameters([
        'track' => '#debateMonumental'
    ])

    // Just add this method to enter in `debug mode`
    ->debugMode()

    ->track(function ($tweet) {
        print_r($tweet);
    });
