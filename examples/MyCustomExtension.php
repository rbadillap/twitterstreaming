<?php

namespace TwitterStreaming\Extensions;

/**
 * This is just an example.
 * Take a look the file CreateExtension.php
 *
 * For something real, you can see the Filters extension
 * @see https://github.com/twitterstreamingphp/twitterstreaming-filters
 */
class MyCustomExtension
{
    public function __construct()
    {
    }

    public function helloWorld()
    {
        print
            'Hi, this is an example about how you can create ' .
            'a TwitterStreaming PHP extension :)' . PHP_EOL
        ;
    }
}
