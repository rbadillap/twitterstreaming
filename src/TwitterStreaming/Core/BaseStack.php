<?php

namespace TwitterStreaming\Core;

use GuzzleHttp\HandlerStack;

/**
 * Use Base Stack to extend the functionality of the stack
 * that we are using to make the request.
 * This is entirely a guzzle business, so its not mandatory to ba called
 * everytime
 *
 * @see http://guzzle.readthedocs.org/en/latest/handlers-and-middleware.html
 * @package TwitterStreaming\Core
 */
class BaseStack
{
    /**
     * Stack that we (and the extensions) will manipulate
     * @var HandlerStack
     */
    public static $stack;

    /**
     * Create the Stack and set a default handler
     */
    public function __construct()
    {
        if (!self::$stack) {
            self::$stack = HandlerStack::create();

            self::$stack->setHandler(\GuzzleHttp\choose_handler());
        }
    }

    /**
     * Returns the current stack
     *
     * @return HandlerStack
     */
    public static function getStack()
    {
        return self::$stack;
    }
}
