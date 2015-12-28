<?php

namespace TwitterStreaming\Extensions;

use TwitterStreaming\Core\BaseBehaviors;
use GuzzleHttp\Client;

/**
 * Behaviors is something pretty similar to our BaseStack
 *
 * Is a set of functions that we can execute in the moment that we get a tweet
 * A project like Filters (https://github.com/twitterstreamingphp/twitterstreaming-filters)
 * uses BaseBehaviors to register all the filters to exclude/include tweets
 * and then, send to the track method.
 *
 * Class MyCustomBehavior
 * @package TwitterStreaming\Extensions
 */
class MyCustomBehavior
{
    // Lets filter the tweets looking for bad words :P
    // We will use Google Profanity API
    public function youCannotSayThat()
    {
        // Register the new behavior
        BaseBehaviors::add(function ($tweet) {

            // I know we can improve this part but this is just an example
            $client = new Client([
                'base_uri' => 'http://www.wdyl.com/'
            ]);

            $request = $client->request('GET', 'profanity', [
                'query' => [
                    'q' => $tweet->text
                ]
            ]);

            // We should return a boolean
            // true to exclude the tweet, false to include it
            $response = json_decode($request->getBody()->getContents());
            return $response->response;

        }, __METHOD__);

        return $this;
    }
}
