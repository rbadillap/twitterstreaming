# TwitterStreaming

TwitterStreaming is a new PHP client that you can use to connect to the [Twitter Streaming API](https://dev.twitter.com/streaming/overview) and retrieve data in real-time.
Throughout this document we'll see that its usage is simple, which can facilitate their integration with multiples systems/workflows.

----------

 - [Installation](#installation)
 - [Features](#features)
	 - [Endpoints](#endpoints)
		 - [How to define an endpoint](#how-to-define-an-endpoint)
	 - [Parameters](#parameters)
	 - [Track](#track)
 - [More examples](#more-examples)
 - [Contributing](#contributing)

## Installation

**TwitterStreaming PHP** is a [Composer](http://getcomposer.org/) package. It will install the package and its dependencies.
The main dependency is [Guzzle](http://guzzlephp.org/) which is a PHP HTTP Client.

To install via composer, run the following command.

    composer require rbadillap/twitterstreaming

You will need to create a [Twitter Application](https://apps.twitter.com/). Follow its indications to get the keys.

**TwitterStreaming PHP** suggests to use [PHP DotEnv](https://github.com/vlucas/phpdotenv) by [Vance Lucas](http://vancelucas.com/) to store you Twitter Application credentials, and keep separately from the code. If you wanna use it, you can include it in your `package.json` typing:

    composer require vlucas/phpdotenv

Then create an `.env` file in the root of your project and put the values that you will see in: https://apps.twitter.com/app/[YOUR_APP_ID]/keys.

```
TWITTERSTREAMING_CONSUMER_KEY=[Consumer Key (API Key)]
TWITTERSTREAMING_CONSUMER_SECRET=[Consumer Secret (API Secret)]
TWITTERSTREAMING_TOKEN=[Access Token]
TWITTERSTREAMING_TOKEN_SECRET=[Access Token Secret]
```

Elsewhere if you won't use this library, you should provide those credentials in the code at the moment you instance the `Tracker` class (take a look the file: `WithoutDotEnv.php` located in the examples folder).
Also, if you wanna provide the credentials despite that you are already using the DotEnv library, you just need to provide those credentials through the instance creation and **TwitterStreaming PHP** will prioritize that.

**TwitterStreaming PHP** will throw an error if were unable to find the `.env` file or the Twitter credentials are invalid. 

## Features

 - Following [PSR-2 Coding Style Guide](http://www.php-fig.org/psr/psr-2/)
 - Following [PSR-4 Autoloader standard](http://www.php-fig.org/psr/psr-4/)
 - Extendible (you can create your own extensions if you want/need).
 - Following the [best practices](https://dev.twitter.com/streaming/overview/connecting) to connect to Twitter API (one single connection of course).
 - Following the Twitter API documentation to [parse the data and responses](https://dev.twitter.com/streaming/overview/processing).
 - ... and so on

## How it works

After give the composer the authority to download the package and generate the [autoloader](https://getcomposer.org/doc/01-basic-usage.md), you will be able to use the namespaces of `TwitterStreaming`.

Something like this.

```php
require_once 'vendor/autoload.php'; // The autoload from composer

use TwitterStreaming\Tracker;

(new Tracker);
```

Obviously, we would like to add more configurations. So you need to work close with the official documentation of Twitter Streaming API: https://dev.twitter.com/streaming/overview to understand how this app works.

### Endpoints

First of all, something very important to understand, is that Twitter gives to you 3 official endpoints.

 - [Public streams](https://dev.twitter.com/streaming/public)
 - [User streams](https://dev.twitter.com/streaming/userstreams)
 - [Sites streams](https://dev.twitter.com/streaming/sitestreams)

In **TwitterStreaming PHP** you can set an endpoint using the method `endpoint` on this way:

```php
require_once 'vendor/autoload.php'; // The autoload from composer

use TwitterStreaming\Tracker;

(new Tracker)
	->endpoint('__endpoint__', '__type__');
```

> **Note:**

> - If you see in the official documentation, there is another API to interact deeper with Twitter, to make searches, read user profile information or post tweets for example, called [REST API](https://dev.twitter.com/rest/public), which doesn't belong to this package due there is a lot of good clients [already written](https://dev.twitter.com/overview/api/twitter-libraries).
> - In some cases the second argument `type` won't be necessary.

#### How to define an endpoint

Depending of the endpoint that you're going to work, you may define the type as well. For example, the Public Endpoint.

> **See:** https://dev.twitter.com/streaming/public

As you can see, this endpoint, provides 3 types: filter, sample and firehose. At this moment, the only functional types are filter and sample. [The type firehose requires special permission from Twitter](https://dev.twitter.com/streaming/reference/get/statuses/firehose).

To define this endpoint you can do something like this;

```php
require_once 'vendor/autoload.php'; // The autoload from composer

use TwitterStreaming\Tracker;

(new Tracker)
	->endpoint('public', 'filter');
```

Also you can add the class directly if you want.

```php
require_once 'vendor/autoload.php'; // The autoload from composer

use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints; // add this line

(new Tracker)
	->endpoint(Endpoints\PublicEndpoint::class, 'filter');
```

Or the User Endpoint, that doesn't need an specific type.

```php
require_once 'vendor/autoload.php'; // The autoload from composer

use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints;

(new Tracker)
	->endpoint(Endpoints\UserEndpoint::class);
```

If you notice, we don't need to specify any URL or any other feature to run an specific endpoint. We have covered all in our library :)

### Parameters

After defining the endpoint, you can continue with the parameters that you'd like to send to Twitter.

An important point to understand is that you can set the parameters based on what Twitter allows to you to use.

For example, if you wants filter tweets using the Public Endpoint and the Filter type, you can see a list of available parameters here: https://dev.twitter.com/streaming/reference/post/statuses/filter you can add all of those parameters in **TwitterStreaming PHP**. How?

```php
require_once 'vendor/autoload.php'; // The autoload from composer

use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints;

(new Tracker)
	->endpoint(Endpoints\PublicEndpoint::class, 'filter')
	->parameters([
		'track' => '#twitter',
		'location' => '-122.75,36.8,-121.75,37.8'
	]);
```

You can filter by two or more hashtags as well, for example.

```php
require_once 'vendor/autoload.php'; // The autoload from composer

use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints;

(new Tracker)
	->endpoint(Endpoints\PublicEndpoint::class, 'filter')
	->parameters([
		'track' => [
			'#twitter', '#facebook', '#instagram'
		],
		'location' => '-122.75,36.8,-121.75,37.8'
	]);
```

This will convert the track to something like: `#twitter OR #facebook OR #instagram`

If you wanna filter tweets with all those hashtags (instead of `OR` use `AND`) just put the words in a same line.

```php
require_once 'vendor/autoload.php'; // The autoload from composer

use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints;

(new Tracker)
	->endpoint(Endpoints\PublicEndpoint::class, 'filter')
	->parameters([
		'track' => '#twitter #facebook #instagram',
		'location' => '-122.75,36.8,-121.75,37.8'
	]);
```

> **See:** https://dev.twitter.com/streaming/overview/request-parameters


### Track

After define the endpoint and parameters, you will be able to track the tweets.
**TwitterStreaming PHP** will throw to you every single tweet, and you can manipulate them as your convenience.

Just use the method `track`.

```php
// track.php
require_once 'vendor/autoload.php'; // The autoload from composer

use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints;

(new Tracker)
	->endpoint(Endpoints\PublicEndpoint::class, 'filter')
	->parameters([
		'track' => '#twitter #facebook #instagram',
		'location' => '-122.75,36.8,-121.75,37.8'
	])
	->track(function($tweet) {
		// Do a print_r($tweet) if you wanna see more
		// details of the tweet.
		print "Tweet details:" . PHP_EOL;
		print "User: @" . $tweet->user->screen_name . PHP_EOL;
		print "Content: " . $tweet->text . PHP_EOL;
	});
```

## More examples

We have attached a folder called `examples` with samples about how **TwitterStreaming PHP** works in different scenarios.

## Contributing

Use the same workflow as many of the packages that we have here in Github.

 1. Fork the project.
 2. Create your feature branch with a related-issue name.
 3. Try to be clear with the code committed and follow the [PSR-2 Coding Style Guide](http://www.php-fig.org/psr/psr-2/).
 4. Run the tests (and create your new ones if necessary).
 5. Commit and push the branch.
 6. Create the Pull Request.
