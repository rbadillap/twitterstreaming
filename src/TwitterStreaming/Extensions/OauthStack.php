<?php


namespace TwitterStreaming\Extensions;

use TwitterStreaming\Core\BaseStack;
use TwitterStreaming\TwitterStreamingException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Dotenv\Dotenv;

final class OauthStack extends BaseStack
{
    public function __construct()
    {
        parent::__construct();

        /**
         * Load the .env files which must contain
         * the token of your Twitter Application
         */
        (new Dotenv(getcwd()))->load();
    }

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
            'TWITTERSTREAMING_CONSUMER_KEY',
            'TWITTERSTREAMING_CONSUMER_SECRET',
            'TWITTERSTREAMING_TOKEN',
            'TWITTERSTREAMING_TOKEN_SECRET'
        ];

        // Nah, this is a simple way to add to $acceptable_tokens
        // the values that we need. Is probably that we are gonna
        // change this due this names are so generic and some
        // applications/frameworks could use the same names
        foreach ($tokens as $value => $token) {
            if (!getenv($token)) {
                var_dump($token);
                throw new TwitterStreamingException(sprintf(
                    'Missing required argument `%s`. Please check your .env file',
                    $token));
            }

            $acceptable_tokens[
                strtolower(str_replace('TWITTERSTREAMING_', '', $token))
            ] = getenv($token);
        }

        return $acceptable_tokens;
    }

    /**
     * Add a new handler, we are gonna use this handler to create
     * a new oAuth instance
     *
     * @see https://github.com/guzzle/oauth-subscriber
     * @throws TwitterStreamingException
     */
    public function useOauth()
    {
        try {

            parent::$stack->push(new Oauth1($this->getAppTokens()));

        } catch (TwitterStreamingException $e) {
            exit($e->getMessage());
        }
    }
}