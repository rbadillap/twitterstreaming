<?php

namespace TwitterStreaming\Core\Traits;

use TwitterStreaming\Core\BaseContainer;
use TwitterStreaming\Core\BaseRequest as Request;
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
    protected $debug = false;

    /**
     * Enable debug mode
     *
     * @return void
     */
    public function debugMode()
    {
        $this->debug = true;
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
        /**
         * Execute useOauth by default
         * Due we decided to include OauthExtension by default
         * we should call its method
         */
        $this->useOauth();

        try {

            $request = new Request($this->debug);
            $request->connect($this->method(), $this->url(), $this->params);

        } catch (TwitterStreamingException $e) {
            exit($e->getMessage());
        }

        // Return the data retrieved and send to the callback
        $request->retrieve(function ($data) use ($func) {
            if (is_callable($func)) {
                /*
                 * Avoid the limit notices
                 * @see https://dev.twitter.com/streaming/overview/messages-types#limit_notices
                 */
                if (is_null($data->limit)) {
                    return call_user_func($func, $data);
                }
            }
        });
    }

    /**
     * Call the method of the library
     *
     * @param $reflection
     * @param $class
     * @param $args
     * @return $this
     */
    protected function call($reflection, $class, $args)
    {
        // Create a new instance of the class (this will execute __construct as well)
        $instance = (new \ReflectionClass($class))->newInstance();

        // We have created the reflection method, so just invoke the method
        $reflection->invokeArgs($instance, $args);

        return $this;
    }

    /**
     * Using the magic method __call we can look for the proper class
     * which contains the method called
     *
     * @param $method
     * @param $args
     * @return EndpointsTrait
     * @throws TwitterStreamingException
     */
    public function __call($method, $args)
    {
        // Get the extensions registered of the BaseContainer class
        $extensions = BaseContainer::getInstance()->getRegistry();

        if (is_array($extensions)) {
            foreach ($extensions as $extension) {
                // Create a new instance of the extension and check if
                // has the method and its public
                $reflection = new \ReflectionMethod($extension, $method);

                if ($reflection && $reflection->isPublic()) {
                    return $this->call($reflection, $extension, $args);
                }
            }

            throw new TwitterStreamingException(sprintf(
                'Unable to find a class with the method `%s`',
                $method
            ));
        }
    }
}