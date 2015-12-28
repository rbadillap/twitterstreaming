<?php

namespace TwitterStreaming\Extensions;

use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TwitterStreaming\Core\BaseStack;

/**
 * Working with the Guzzle's HandlerStack
 * is basically working with BaseStack class
 *
 * We take care of initialize an instance once, of HandlerStack
 * so you can manipulate in the way that you want the set of
 * items that has been pushed on the stack originally.
 *
 * You can take a look the file OauthStack.php on this project, it uses the HandlerStack
 * /Extensions/OauthStack.php
 *
 * @see http://docs.guzzlephp.org/en/latest/handlers-and-middleware.html#handlerstack
 *
 * Class MyGuzzleMiddleware
 * @package TwitterStreaming\Extensions
 */
class MyGuzzleMiddleware extends BaseStack
{
    public function __construct()
    {
        // Necesary to retrieve the stack (or create the new instance if doesnt exists)
        parent::__construct();
    }

    public function sayHelloBeforeTheRequest()
    {
        parent::$stack->push(Middleware::mapRequest(function (RequestInterface $request) {

            print 'Hello' . PHP_EOL;

            return $request;
        }));
    }

    public function notifyWhenResponseIsComplete()
    {
        parent::$stack->push(Middleware::mapResponse(function (ResponseInterface $response) {

            print 'Response!' . PHP_EOL;

            return $response;
        }));
    }
}
