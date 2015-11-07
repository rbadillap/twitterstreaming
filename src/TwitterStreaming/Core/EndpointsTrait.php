<?php

namespace TwitterStreaming\Core;

use TwitterStreaming\Core\TwitterStreamingRequest as Request;
use TwitterStreaming\TwitterStreamingException;

trait EndpointsTrait
{

	/**
	 * Type of endpoint to request
	 *
	 * @see $docsUrl
	 * @var string
	 */
	protected $type;

	/**
	 * Parameters to make the request to the API
	 *
	 * @var array
	 */
	private $params = [];

	/**
	 * Define if process the request as debug mode
	 *
	 * @var bool
	 */
	protected $debug = FALSE;

	/**
	 * Enable debug mode
	 *
	 * @return void
	 */
	public function debugMode()
	{
		$this->debug = TRUE;
		return $this;
	}

	/**
	 * Define the parameters for the request
	 * Those parameters depends entirely of the type of endpoint declared
	 *
	 * @param array $params
	 */
	public function parameters(array $params)
	{
		$this->params = $params;

		return $this;
	}

	/**
	 * Track is the main method, you should use it with your custom logic.
	 * This method will return the $tweet data, you can use this method
	 * to store the tweets in your database, for example.
	 *
	 * @param $func
	 * @throws TwitterStreamingException
	 */
	public function track($func)
	{
		try {

			$request = new Request($this->debug);
			$request->connect($this->method(), $this->url(), $this->params);

		} catch (TwitterStreamingException $e) {
			exit($e->getMessage());
		}

		// Return the data retrieved and send to the callback
		$request->retrieve(function ($data) use ($func) {
			if (is_callable($func)) {
				return call_user_func($func, $data);
			}
		});
	}
}