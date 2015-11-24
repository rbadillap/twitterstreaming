<?php

namespace TwitterStreaming\Core;

class BaseBehaviors
{
    private $stack = [];

    public function add(callable $method, $name = '')
    {
        $this->stack[] = [$method, $name];
    }

    public function getStack()
    {
        return $this->stack;
    }

    public static function resolve($stream)
    {
        foreach (static::getStack() as $behavior) {
            call_user_func_array($behavior[0], [$stream]);
        }
    }
}