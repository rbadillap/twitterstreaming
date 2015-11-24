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
    private $stack = [];

    /**
     * Add new function to the stack to be called later
     *
     * @param callable $method
     * @param string $name
     */
    public function add(callable $method, $name = '')
    {
        $this->stack[] = [$method, $name];
    }

    /**
     * Return the current stack
     *
     * @return array
     */
    public function getStack()
    {
        return $this->stack;
    }

    /**
     * Resolve is the method to call all the functions registered in the stack
     * This just loop the current stack and executes the functions registered.
     *
     * @param $content
     */
    public static function resolve($content)
    {
        foreach (static::getStack() as $behavior) {
            call_user_func($behavior[0], [$content]);
        }
    }
}