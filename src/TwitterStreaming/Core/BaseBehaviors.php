<?php

namespace TwitterStreaming\Core;

/**
 * Use BaseBehaviors if you wanna do something before to show the data
 * already retrieved. For example, if you wanna filter by some custom
 * and specific logic.
 * The extension Filters uses this.
 *
 * Class BaseBehaviors
 * @package TwitterStreaming\Core
 */
class BaseBehaviors
{
    /** @var array  */
    public static $stack = [];

    /**
     * Add new function to the stack to be called later
     *
     * @param callable $method
     * @param string $name
     */
    public static function add(callable $method, $name = '')
    {
        static::$stack[] = [$method, $name];
    }

    /**
     * Resolve is the method to call all the functions registered in the stack
     * This just loop the current stack and executes the functions registered.
     *
     * @param $content
     * @return bool
     */
    public static function resolve($content)
    {
        foreach (static::$stack as $behavior) {
            if (!call_user_func_array($behavior[0], [$content])) {
                return false;
            }
        }

        return true;
    }
}
