<?php

namespace TwitterStreaming\Extensions;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

class FilterCallback implements StreamInterface
{
    use StreamDecoratorTrait;

    private $callback;

    public function __construct(StreamInterface $stream, callable $callback)
    {
        $this->stream = $stream;
        $this->callback = $callback;
    }

    public function read($length)
    {
        $result = $this->stream->read($length);

        call_user_func_array($this->callback, $result);

        return $result;
    }
}