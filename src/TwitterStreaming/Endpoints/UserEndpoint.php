<?php

namespace TwitterStreaming\Endpoints;

use TwitterStreaming\Core\Traits\DebugEndpointsTrait;
use TwitterStreaming\Core\Traits\EndpointsTrait;

final class UserEndpoint
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
     * @var string
     */
    public function docsUrl()
    {
        return 'https://dev.twitter.com/streaming/userstreams';
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
            'endpoint' => 'user.json'
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

    public function __construct()
    {
    }
}
