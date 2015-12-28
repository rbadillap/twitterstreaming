<?php

namespace TwitterStreaming;

abstract class Config
{
    /**
     * Public endpoint
     *
     * @see https://dev.twitter.com/streaming/public
     * @var string
     */
    const PUBLIC_ENDPOINT = 'public';

    /**
     * User endpoint
     *
     * @see https://dev.twitter.com/streaming/userstreams
     * @var string
     */
    const USER_ENDPOINT = 'user';

    /**
     * Site endpoint
     *
     * @see https://dev.twitter.com/streaming/sitestreams
     * @var string
     */
    const SITE_ENDPOINT = 'site';

    /**
     * Sample type Public Endpoint
     *
     * @see https://dev.twitter.com/streaming/reference/get/statuses/sample
     * @var string
     */
    const PUBLIC_ENDPOINT_TYPE_SAMPLE = 'sample';

    /**
     * Filter type Public Endpoint
     *
     * @see https://dev.twitter.com/streaming/reference/post/statuses/filter
     * @var string
     */
    const PUBLIC_ENDPOINT_TYPE_FILTER = 'filter';
}
