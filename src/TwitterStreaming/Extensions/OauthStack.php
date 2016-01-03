<?php


namespace TwitterStreaming\Extensions;

use TwitterStreaming\Core\BaseStack;
use TwitterStreaming\TwitterStreamingException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Dotenv\Dotenv;

final class OauthStack extends BaseStack
{
    /**
     * Credentials will be false by default
     * and gonna be populated if app credentials are provided
     * @var array|bool|null
     */
    protected $credentials = false;

    /**
     * OauthStack constructor.
     * @param array|null $credentials
     * @throws TwitterStreamingException
     */
    public function __construct(array $credentials = null)
    {
        parent::__construct();

        try {
            // Check if Dotenv library is loaded
            // If credentials are provided, let's prioritize those
            // values instead of take in consideration the Dotenv library
            if (class_exists('Dotenv\Dotenv') && ! $credentials) {
                /**
                 * Load the .env files which must contain
                 * the token of your Twitter Application
                 */
                if (file_exists(getcwd() . DIRECTORY_SEPARATOR . '.env')) {
                    (new Dotenv(getcwd()))->load();
                } else {
                    (new Dotenv(dirname(getcwd())))->load();
                }
            } else {
                // If Dotenv library are not loaded, the credentials should
                // be provided in the constructor of the Tracker
                if (! is_array($credentials) || ! count($credentials)) {
                    throw new TwitterStreamingException(sprintf(
                        'TwitterStreaming suggests to use vlucas/phpdotenv ' .
                        'library to store you Twitter app credentials. ' .
                        'If you do not want to use it, you should provide those ' .
                        'values in the Tracker instance. Please take a look the example: ' .
                        '"examples/WithoutDotEnv.php"'
                    ));
                }

                $this->credentials = $credentials;
            }
        } catch (TwitterStreamingException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Retrieve from the registry of the token
     * Basically if the credentials variable is a valid array
     * that means that the credentials were provided by the instance
     *
     * @param $name
     * @return string
     */
    protected function loadTokensRegistry($name)
    {
        // Check if credentials variable is a valid array
        if ($this->credentials) {
            return $this->credentials[$name];
        }

        // If don't, return the value from the $_ENV (using DotEnv library)
        return getenv($name);
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
            if (! $this->loadTokensRegistry($token)) {
                throw new TwitterStreamingException(sprintf(
                    'Missing required argument `%s`. Please check your .env file',
                    $token));
            }

            $acceptable_tokens[strtolower(str_replace('TWITTERSTREAMING_', '',
                $token))] = $this->loadTokensRegistry($token);
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
