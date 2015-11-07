<?php

namespace TwitterStreaming\Endpoints;

use TwitterStreaming\Core\DebugEndpointsTrait;
use TwitterStreaming\Core\EndpointsTrait;
use TwitterStreaming\TwitterStreamingConfig as Config;
use TwitterStreaming\TwitterStreamingException;

final class PublicEndpoint
{
	use EndpointsTrait, DebugEndpointsTrait;

	/**
	 * List of types that this endpoint allows
	 *
	 * @var array
	 */
	public static $allowedTypes = [
		Config::PUBLIC_ENDPOINT_TYPE_FILTER,
		Config::PUBLIC_ENDPOINT_TYPE_SAMPLE
	];

	/**
	 * Current url of documentation regarding this endpoint
	 *
	 * @var string
	 */
	public function docsUrl()
	{
		return 'https://dev.twitter.com/streaming/public';
	}

	/**
	 * API url of the type to work on
	 *
	 * @return string
	 */
	public function url()
	{
		if ($this->type == Config::PUBLIC_ENDPOINT_TYPE_FILTER) {
			return [
				'api' => 'https://stream.twitter.com/1.1/',
				'endpoint' => 'statuses/filter.json'
			];
		}

		if ($this->type == Config::PUBLIC_ENDPOINT_TYPE_SAMPLE) {
			return [
				'api' => 'https://stream.twitter.com/1.1/',
				'endpoint' => 'statuses/sample.json'
			];
		}

		return false;
	}

	/**
	 * Define the method to use depending of the type of request
	 *
	 * @return string
	 */
	public function method()
	{
		if ($this->type == 'filter') {
			return 'POST';
		}

		if ($this->type == 'sample') {
			return 'GET';
		}
	}

	public function __construct($type = '')
	{
		if (!in_array($type, self::$allowedTypes)) {
			throw new TwitterStreamingException(
				'The given `type` parameter does not belong ' .
				"to any valid User Endpoint type.\nShould be any of the following: " .
				implode(', ', self::$allowedTypes) . '. More info: ' . $this->docsUrl()
			);
		}

		$this->type = $type;
	}
}