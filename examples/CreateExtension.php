<?php
// $ php CreateExtension.php

require_once 'autoload.php';

use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints;
use TwitterStreaming\Extensions;

(new Tracker)
    // Add the new extension. See the file MyCustomExtension.php
    ->addExtension(Extensions\MyCustomExtension::class)

    // Public endpoint
    ->endpoint(Endpoints\PublicEndpoint::class, 'filter')

    // My custom method created by MyCustomExtension.php
    ->helloWorld()

    // Track the tweets
    ->track(function ($tweet) {
        // Print the tweet object
        print_r($tweet);
    });
