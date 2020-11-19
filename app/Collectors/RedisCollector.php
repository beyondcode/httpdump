<?php

namespace App\Collectors;

use App\Exceptions\InvalidDumpException;
use App\Models\Dump;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisCollector implements CollectorContract
{

    const PREFIX = 'httpdump';

    protected function key(string $name): string
    {
        return static::PREFIX . '_' . $name;
    }

    protected function countKey(): string
    {
        return static::PREFIX . '-request_count';
    }

    public function createDump(): Dump
    {
        $dump = new Dump();

        Redis::connection()->set($this->key($dump->name), serialize($dump));
        Redis::connection()->expire($this->key($dump->name), config('httpdump.ttl_in_seconds'));

        return $dump;
    }

    public function createRequest(Dump $dump, Request $request)
    {
        $dump->addRequest($request);

        Redis::connection()->set($this->key($dump->name), serialize($dump));
        Redis::connection()->expire($this->key($dump->name), config('httpdump.ttl_in_seconds'));

        Redis::connection()->setnx($this->countKey(), 0);
        Redis::connection()->incr($this->countKey());
    }

    public function countDumps(): int
    {
        return count(Redis::connection()->keys(static::PREFIX.'_*'));
    }

    public function countRequests(): int
    {
        return int(Redis::connection()->get($this->countKey()));
    }

    public function getDump(string $name): Dump
    {
        $key = $this->key($name);

        $serializedDump = Redis::connection()->get($key);
        try {
            return unserialize($serializedDump);
        } catch (\Throwable $e) {
            throw new InvalidDumpException();
        }
    }

    public function clearRequests(Dump $dump)
    {
        $dump->requests = [];

        Redis::connection()->set($this->key($dump->name), serialize($dump));
        Redis::connection()->expire($this->key($dump->name), config('httpdump.ttl_in_seconds'));
    }
}
