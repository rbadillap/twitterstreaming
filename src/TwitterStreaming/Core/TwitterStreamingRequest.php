<?php

namespace TwitterStreaming\Core;

use TwitterStreaming\TwitterStreamingException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * This is the main class to execute the request
 * Also we parse the tweets following the official documentation
 *
 * @see https://dev.twitter.com/streaming/overview/processing
 *
 * Class TwitterStreamingRequest
 * @package TwitterStreaming\Core
 */
class TwitterStreamingRequest
{
	/**
	 * Key reference which will change depending of the method
	 * to send to the request
	 *
	 * @see http://docs.guzzlephp.org/en/latest/request-options.html?highlight=form_params#query
	 * @see http://docs.guzzlephp.org/en/latest/request-options.html?highlight=form_params#form-params
	 * @var string
	 */
	protected $flag;

	/**
	 * Client handler
	 *
	 * @var string
	 */
	protected $client;

	/**
	 * Request method
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * Endpoint url
	 *
	 * @var array
	 */
	protected $url = [];

	/**
	 * Parameters to send to Twitter Stream API
	 *
	 * @var array
	 */
	protected $parameters = [];

	/**
	 * DebugMode
	 *
	 * @var bool
	 */
	protected $debugMode = FALSE;

	function __construct($debugMode)
	{
		$this->debugMode = $debugMode;
	}

	/**
	 * Convert the memory usage bytes in some
	 * more readable value
	 *
	 * @param $size
	 * @return string
	 */
	public function toHuman($size)
	{
		$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		return $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
	}

	/**
	 * Return the Twitter App tokens
	 *
	 * @return array
	 * @throws TwitterStreamingException
	 */
	protected function getAppTokens()
	{
		// .env file could have more values stored
		$acceptable_tokens = [];

		// We only need these values
		$tokens = [
			'CONSUMER_KEY', 'CONSUMER_SECRET', 'TOKEN', 'TOKEN_SECRET'
		];

		// Nah, this is a simple way to add to $acceptable_tokens
		// the values that we need. Is probably that we are gonna
		// change this due this names are so generic and some
		// applications/frameworks could use the same names
		foreach ($tokens as $value => $token) {
			if (!getenv($token)) {
				throw new TwitterStreamingException(
					'Missing required argument `' . $token .
					'`. Please check your .env file'
				);
			}

			$acceptable_tokens[strtolower($token)] = getenv($token);
		}

		return $acceptable_tokens;
	}

	public function connect($method, $url, array $parameters = [])
	{
		$this->method = $method;

		$this->url = $url;

		$this->parameters = $parameters;

		$this->flag = $this->method == 'POST' ? 'form_params' : 'query';

		try {

			/**
			 * Create a new handler, we are gonna use this handler to create
			 * a new oAuth instance
			 *
			 * @see https://github.com/guzzle/oauth-subscriber
			 */
			$stack = HandlerStack::create();

			$middleware = new Oauth1($this->getAppTokens());
			$stack->push($middleware);

			/**
			 * All the request must have these values, so let's assign
			 * them here and save memory :)
			 */
			$this->client = new Client([
				'base_uri' => $this->url['api'],
				'handler' => $stack,
				'auth' => 'oauth',
			]);

		} catch (Exception $e) {
			throw new TwitterStreamingException($e->getMessage());
		}

		return $this;
	}

	public function retrieve($func)
	{
		try {

			/** @var $extra_params */
			$extra_params = [];

			// Debug mode TRUE/FALSE
			$extra_params['debug'] = (bool)$this->debugMode;

			/**
			 * This is the way that we are gonna retrieve the tweets.
			 * We need the stream size to know how many bytes we
			 * are gonna read in the tweet content.
			 *
			 * @see https://dev.twitter.com/streaming/overview/processing#delimited
			 */
			$extra_params[$this->flag]['delimited'] = 'length';

			/**
			 * Form params are the parameters that we are gonna send to
			 * the Twitter API
			 *
			 * @see https://dev.twitter.com/streaming/overview/request-parameters
			 * @var  $params
			 * @var  $value
			 */
			foreach ($this->parameters as $params => $value) {
				$extra_params[$this->flag][$params] = $value;
			}

			$request = new Request($this->method, $this->url['endpoint']);

			// Modify the headers and some parameters that should be set
			// by this way
			$response = $this->client->send($request, array_merge([
				'headers' => [
					'Accept' => '*/*',
				],
				'stream' => true,
				'verify' => false,
			], $extra_params));

			// Get the body of the stream
			$stream = $response->getBody();

			// Set the length, we are gonna need this value then
			$length = '';

			// Run until stream are finished, which it's rare due
			// we are using a Keep-Alive connection
			while (!$stream->eof()) {

				/**
				 * Step by step.
				 * Lets concatenate character by character until to know
				 * that we got the amount of bytes required to read the
				 * message
				 *
				 * @see https://dev.twitter.com/streaming/overview/request-parameters#delimited
				 */
				$length .= $stream->read(1);

				/*
				 * And how can we know where the amount of bytes are finished and the next
				 * is the message? Well, based on the twitter documentation, with a simple
				 * \r\n right after the number
				 */
				if (strpos($length, PHP_EOL) !== FALSE) {


					// $length is now out bytes value, lets use to read the message
					$length = intval($length);

					/**
					 * Why we are here and we are validating length?
					 * That's because some cases Twitter send signals to
					 * our library to prevent our network to close our
					 * connection our stop our script.
					 *
					 * @see https://dev.twitter.com/streaming/overview/messages-types#blank_lines
					 */
					if ($length > 0 && is_callable($func)) {

						/*
						 * Let's decode the json sent by the request
						 * and also lets trim that message due the last character
						 * is an \r\n which could affect out main logic
						 */
						call_user_func($func, json_decode(trim($stream->read($length))));

						if ($this->debugMode) {
							print
								"Memory get peak usage: " .
								$this->toHuman(memory_get_peak_usage(TRUE)) . PHP_EOL . PHP_EOL;
						}

						// Reset length and start again with a new tweet
						$length = '';
					}
				}
			}

		} catch (Exception $e) {
			throw new TwitterStreamingException($e->getMessage());
		}
	}
}