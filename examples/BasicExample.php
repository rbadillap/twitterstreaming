<?php
// $ php BasicExample.php

require_once 'autoload.php';

use TwitterStreaming\Tracker;

$tracker = new Tracker();

$tracker
    ->endpoint('user')
    ->track(function ($tweet) {
        // Print the tweet object
        print_r($tweet);
    });
