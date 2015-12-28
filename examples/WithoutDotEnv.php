<?php
// $ php WithoutDotEnv.php

// (vlucas/phpdotenv is not loaded in vendors)
require_once 'autoload.php';

use TwitterStreaming\Tracker;

/**
 * @see https://apps.twitter.com
 */
$tracker = new Tracker([
    'TWITTERSTREAMING_CONSUMER_KEY' => 'Your consumer key',
    'TWITTERSTREAMING_CONSUMER_SECRET' => 'Your consumer secret',
    'TWITTERSTREAMING_TOKEN' => 'Your app token',
    'TWITTERSTREAMING_TOKEN_SECRET' => 'Your app token secret'
]);

$tracker
    ->endpoint('user')
    ->track(function ($tweet) {
        // Print the tweet object
        print_r($tweet);
    });
