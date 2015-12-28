<?php

namespace TwitterStreaming\Core;

use TwitterStreaming\TwitterStreamingException;
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
final class BaseRequest
{
    /**
     * Key reference which will change depending of the method
     * to send to the request
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#query
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#form-params
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
    protected $debugMode = false;

    public function __construct($debugMode)
    {
        $this->debugMode = $debugMode;
    }

    /**
     * Parse the track flag
     *
     * A comma-separated list of phrases which will be used to determine what Tweets will be
     * delivered on the stream. A phrase may be one or more terms separated by spaces,
     * and a phrase will match if all of the terms in the phrase are present in the Tweet,
     * regardless of order and ignoring case.
     * By this model, you can think of commas as logical ORs, while spaces are equivalent
     * to logical ANDs (e.g. ‘the twitter’ is the AND twitter, and ‘the,twitter’ is the OR twitter).
     *
     * @see https://dev.twitter.com/streaming/overview/request-parameters
     * @param $value
     * @return string
     */
    private function parseTracks($value)
    {
        if (is_array($value)) {
            return implode(',', $value);
        }

        return $value;
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
        $filesizename = [
            " Bytes",
            " KB",
            " MB",
            " GB",
            " TB",
            " PB",
            " EB",
            " ZB",
            " YB"
        ];

        return $size ?
            round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] :
            '0 Bytes';
    }

    public function connect($method, $url, array $parameters = [])
    {
        $this->method = $method;

        $this->url = $url;

        $this->parameters = $parameters;

        $this->flag = $this->method == 'POST' ? 'form_params' : 'query';

        try {

            /**
             * Load the stack from BaseStrack class
             */
            $stack = BaseStack::getStack();

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
                if ($params == 'track') {
                    $value = $this->parseTracks($value);
                }

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
                if (strpos($length, PHP_EOL) !== false) {


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
                                $this->toHuman(memory_get_peak_usage(true)) . PHP_EOL . PHP_EOL;
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
