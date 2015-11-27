<?php
// $ php UsingProxy.php

require_once 'autoload.php';

use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints;

/**
 * Guzzle should take care of the request
 * So basically you just need to add in the parameters the options that you
 * want supported by Guzzle
 *
 * @see http://docs.guzzlephp.org/en/latest/request-options.html
 */
(new Tracker)
    ->endpoint(Endpoints\PublicEndpoint::class, 'filter')
    ->parameters([
        'track' => 'twitter',

        'proxy' => 'tcp://localhost:8125',

        // Just follow the Guzzle documentation
        // You can use this too.
//        'proxy' => [
//            'http'  => 'tcp://localhost:8125', // Use this proxy with "http"
//            'https' => 'tcp://localhost:9124', // Use this proxy with "https",
//            'no' => ['.mit.edu', 'foo.com']    // Don't use a proxy with these
//        ]
    ])
    ->track(function ($tweet) {
        // Print the tweet object
        print_r($tweet);
    });
