<?php

/**
 * TwitterStreaming
 *
 * PHP library to connect to the Twitter Streaming API
 * and retrieve data in real-time.
 *
 * @version 0.1.5
 * @license MIT
 */
namespace TwitterStreaming;

use TwitterStreaming\Core\BaseContainer;
use TwitterStreaming\Extensions;

class Tracker
{
    /**
     * base container which will contain the instance of the BaseContainer class
     *
     * @var mixed
     */
    protected $baseContainer;

    /**
     * Tracker constructor.
     * @param array|null $credentials
     */
    public function __construct(array $credentials = null)
    {
        // Singleton pattern to the base container we don't need
        // to instantiate the base container a lot of times
        $this->baseContainer = BaseContainer::getInstance();

        // Register the OauthStack extension by default
        $this->registerExtension(Extensions\OauthStack::class);

        /*
         * Execute useOauth by default
         * Due we decided to include OauthExtension by default
         * we should call its method
         */
        (new Extensions\OauthStack($credentials))->useOauth();
    }

    /**
     * Register a new extension, use the BaseContainer to map the new class
     *
     * @param string $class
     * @return $this
     */
    public function registerExtension($class)
    {
        $this->baseContainer->register($class);

        return $this;
    }

    /**
     * Create an alias of registerExtension
     *
     * @param string $class
     * @return Tracker
     */
    public function addExtension($class)
    {
        return $this->registerExtension($class);
    }

    /**
     * Map of the endpoints. The given parameter belongs to
     * an specific endpoint class.
     *
     * @param string $_key
     * @param string $type
     * @return class
     */
    private function mapEndpoints($_key, $type)
    {
        $endpoints = [
            Config::PUBLIC_ENDPOINT => Endpoints\PublicEndpoint::class,
            Config::USER_ENDPOINT => Endpoints\UserEndpoint::class,
            Config::SITE_ENDPOINT => Endpoints\SiteEndpoint::class,
        ];

        return new $endpoints[$_key]($type);
    }

    /**
     * Mapped list of available endpoints by Twitter Streaming API.
     * This method is just to compare the given parameter or to list outside.
     *
     * @return array
     */
    public function availableEndpoints()
    {
        return [
            Config::PUBLIC_ENDPOINT,
            Config::USER_ENDPOINT,
            Config::SITE_ENDPOINT
        ];
    }

    /**
     * Use the given endpoint.
     * Check if the parameter is an object or a mapped available endpoint.
     *
     * @param $endpoint
     * @param $type
     * @throws TwitterStreamingException
     * @return object
     */
    public function endpoint($endpoint, $type = '')
    {
        if (is_string($endpoint)) {
            // If is an existent class, return its instance
            if (class_exists($endpoint)) {
                return new $endpoint($type);
            }

            // Check if the parameter match with the list
            // of available endpoints
            if (! in_array($endpoint, $this->availableEndpoints())) {
                throw new TwitterStreamingException(
                    'Incorrect endpoint, should be any of the following: ' .
                    implode(', ', $this->availableEndpoints()) . '.'
                );
            }

            // If endpoint param are correct, create new instance.
            return $this->mapEndpoints($endpoint, $type);
        }
    }
}
