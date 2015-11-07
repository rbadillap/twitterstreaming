<?php

namespace TwitterStreaming\Endpoints;

final class UserEndpoint
{
	/**
	 * Current url of documentation regarding this endpoint
	 *
	 * @var string
	 */
	public $docsUrl = 'https://dev.twitter.com/streaming/userstreams';

	/**
	 * API url of the type to work on
	 *
	 * @return string
	 */
	public function url()
	{
		return 'https://userstream.twitter.com/1.1/user.json';
	}

	public function __construct()
	{
		print "Hello: " . __CLASS__;
	}
}