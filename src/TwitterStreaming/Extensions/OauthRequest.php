<?php

namespace TwitterStreaming\Extensions;

use TwitterStreaming\Core\BaseExtension;
use TwitterStreaming\Core\Traits\ExtensionsTrait;

class OauthRequest extends BaseExtension
{
    use ExtensionsTrait;

    /**
     * Return the Twitter App tokens
     *
     * @return array
     * @throws TwitterStreamingException
     */
    protected function getAppTokens()
    {
        // .env file could have more values stored
        $acceptable_tokens = [];

        // We only need these values
        $tokens = [
            'CONSUMER_KEY',
            'CONSUMER_SECRET',
            'TOKEN',
            'TOKEN_SECRET'
        ];

        // Nah, this is a simple way to add to $acceptable_tokens
        // the values that we need. Is probably that we are gonna
        // change this due this names are so generic and some
        // applications/frameworks could use the same names
        foreach ($tokens as $value => $token) {
            if (!getenv($token)) {
                throw new TwitterStreamingException(
                    'Missing required argument `' . $token .
                    '`. Please check your .env file'
                );
            }

            $acceptable_tokens[strtolower($token)] = getenv($token);
        }

        return $acceptable_tokens;
    }

    public function __construct()
    {
        parent::__construct();
        var_dump(parent::$stack);
    }

    public function execute()
    {
        var_dump('execute!!!');
        return $this->stack->push(new Oauth1($this->getAppTokens()));
    }

}