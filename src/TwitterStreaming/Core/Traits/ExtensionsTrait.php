<?php

namespace TwitterStreaming\Core\Traits;


trait ExtensionsTrait
{
    public function init()
    {
        if (method_exists($this, 'execute')) {
            return call_user_func_array([$this, 'execute'], []);
        }
        exit;
    }
}