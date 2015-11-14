<?php

namespace TwitterStreaming\Core;

use GuzzleHttp\HandlerStack;

use TwitterStreaming\Core\Traits\ExtensionsTrait;

abstract class BaseExtension
{
    protected $stack;

    public function __construct()
    {
        $this->stack = HandlerStack::create();
    }

    public function init()
    {
        exit;
    }
}