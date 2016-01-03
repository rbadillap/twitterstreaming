<?php

namespace TwitterStreaming\Endpoints;

use TwitterStreaming\Core\Traits\DebugEndpointsTrait;
use TwitterStreaming\Core\Traits\EndpointsTrait;
use TwitterStreaming\TwitterStreamingException;

final class SiteEndpoint
{
    use EndpointsTrait, DebugEndpointsTrait;

    /**
     * List of types that this endpoint allows
     *
     * @var array
     */
    public static $allowedTypes = [];

    /**
     * Current url of documentation regarding this endpoint
     *
     * @string string
     */
    public function docsUrl()
    {
        return 'https://dev.twitter.com/streaming/sitestreams';
    }

    /**
     * API url of the type to work on
     *
     * @return string
     */
    public function url()
    {
        return [
            'api' => 'https://userstream.twitter.com/1.1/',
            'endpoint' => 'site.json'
        ];
    }

    /**
     * Define the method to use depending of the type of request
     *
     * @return string
     */
    public function method()
    {
        return 'GET';
    }

    public function __construct($type = '')
    {
        /**
         * By the moment, let's throw an exception due not all
         * the applications could be accepted by Twitter to use
         * this endpoint.
         *
         * Anyway we are gonna try to give the proper support :)
         *
         * @see https://dev.twitter.com/streaming/sitestreams
         */
        throw new TwitterStreamingException(
            'Site Stream is currently in beta version ' .
            'so, applications are no longer accepted.' . PHP_EOL .
            'More info: ' . $this->docsUrl()
        );
    }
}
