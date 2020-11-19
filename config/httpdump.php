<?php

return [
    /**
     * The HTTP request collector to use. By default, the requests
     * get stored in Redis.
     */
    'collector' => \App\Collectors\RedisCollector::class,

    /**
     * The maximum amount of HTTP dumps to store per dump.
     * Only the X most recent dumps will be kept.
     */
    'max_dumps' => 10,

    /**
     * To avoid storing huge amounts of request data in Redis,
     * you can limit the maximum request size to accept.
     */
    'max_request_size_in_kb' => 512,

    /**
     * The TTL of the dumps before they get automatically deleted.
     */
    'ttl_in_seconds' => \Carbon\CarbonInterval::days(2)->totalSeconds,
];
