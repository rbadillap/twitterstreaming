<?php

namespace TwitterStreaming\Core\Traits;

/**
 * Class DebugEndpointsTrait
 *
 * This trait is helpful when you want to debug
 * @package TwitterStreaming
 */
trait DebugEndpointsTrait
{
    abstract public function docsUrl();

    final public function getDocsUrl()
    {
        print $this->docsUrl();
        print PHP_EOL;
        return $this;
    }

    abstract public function method();

    public function getMethod()
    {
        print $this->method();
        print PHP_EOL;
        return $this;
    }

    abstract public function url();

    public function getUrl()
    {
        print_r($this->url());
        print PHP_EOL;
        return $this;
    }

    abstract public function parameters(array $params);

    public function getParameters()
    {
        if (empty($this->params)) {
            print
                "Parameters are not defined yet. " .
                "Use the `parameters` method to define them" . PHP_EOL;
        }
        print_r($this->params);
        print PHP_EOL;
        return $this;
    }

    public function getInfo()
    {
        print "Info to send to Twitter Streaming API" . PHP_EOL;
        $this->getDocsUrl()
            ->getMethod()
            ->getUrl()
            ->getParameters();
    }
}
