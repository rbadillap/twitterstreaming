<?php

namespace TwitterStreaming\Core;

use TwitterStreaming\TwitterStreamingException;

/**
 * The 'repo' which will handle the extensions that we are gonna use
 *
 * @package TwitterStreaming\Core
 */
final class BaseContainer
{
    /**
     * Registry of the classes
     *
     * @var array
     */
    public static $registry = [];

    /**
     * The instance of this class
     *
     * @var object
     */
    private static $instance;

    /**
     * Follow the singleton pattern to return the instance of
     * this class. We are gonna use it many times
     *
     * @param null $arguments
     * @return mixed
     */
    public static function getInstance($arguments = null)
    {
        // Check if there are a non existent instance created
        if (!isset(self::$instance)) {
            self::$instance = new self($arguments);
        }

        return self::$instance;
    }

    /**
     * Register a new container
     *
     * @param string $name
     */
    public static function register($name)
    {
        // $name should be an existent class
        try {
            if (!class_exists($name)) {
                throw new \InvalidArgumentException(sprintf(
                    "`%s` is not a valid class", $name
                ));
            }

            static::$registry[] = $name;

        } catch (TwitterStreamingException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Returns the registry
     *
     * @return array
     */
    public function getRegistry()
    {
        return static::$registry;
    }

    public function __get($name)
    {
        return $this->getRegistry();
    }

    public function __set($name, $value)
    {
        return $this->register($value);
    }

    public function __toString()
    {
        return print_r($this->getRegistry(), true);
    }
}
