<?php

/**
 * TwitterStreaming
 *
 * PHP library to connect to the Twitter Streaming API
 * and retrieve data in real-time.
 *
 * @version 0.1.0
 * @license MIT
 */
namespace TwitterStreaming;

use TwitterStreaming\TwitterStreamingException;
use TwitterStreaming\TwitterStreamingConfig as Config;
use Dotenv\Dotenv;

class TwitterStreaming
{
	function __construct()
	{
		/**
		 * Load the .env files which must contain
		 * the token of your Twitter Application
		 */
		(new Dotenv(getcwd()))->load();
	}

	/**
	 * Map of the endpoints. The given parameter belongs to
	 * an specific endpoint class.
	 *
	 * @param $_key
	 * @return void
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
	 * Mapped list of available endpoints by Twitter Streamming API.
	 * This method is just to compare the given parameter or to list outside.
	 *
	 * @return array
	 */
	public function availableEndpoints()
	{
		return [
			Config::PUBLIC_ENDPOINT, Config::USER_ENDPOINT, Config::SITE_ENDPOINT
		];
	}

	/**
	 * Use the given endpoint.
	 * Check if the parameter is an object or a mapped available endpoint.
	 *
	 * @param $endpoint
	 * @throws TwitterStreamingException
	 * @return void
	 */
	public function useTheEndpoint($endpoint, $type = '')
	{
		if (is_string($endpoint)) {
			// If is an existent class, return its instance
			if (class_exists($endpoint)) {
				return new $endpoint($type);
			}

			// Check if the parameter match with the list
			// of availables endpoints
			if (!in_array($endpoint, $this->availableEndpoints())) {
				throw new TwitterStreamingException(
					'Incorrect endpoint, should be any of the following: ' .
					implode(', ', $this->availableEndpoints()) . '.'
				);
			}

			// If endpoint param are correct, create new instance.
			$this->mapEndpoints($endpoint, $type);

			return $this;
		}
	}
}