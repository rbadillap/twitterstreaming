<?php

namespace TwitterStreaming\Endpoints;

use TwitterStreaming\EndpointsInterface;

final class SiteEndpoint implements EndpointsInterface
{
	/**
	 * Current url of documentation regarding this endpoint
	 *
	 * @var string
	 */
	public $docsUrl = 'https://dev.twitter.com/streaming/sitestreams';

	/**
	 * API url of the type to work on
	 *
	 * @return string
	 */
	public function url()
	{
		return 'https://sitestream.twitter.com/1.1/site.json';
	}

	public function __construct()
	{
		print "Hello: " . __CLASS__ ;
	}
}