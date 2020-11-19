<?php

namespace App\Models;

use App\LoggedRequest;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Dump implements Arrayable
{
    public $name;

    public $requests = [];

    public function __construct()
    {
        $this->name = (string)Str::uuid();
    }

    public function addRequest(\Illuminate\Http\Request $request)
    {
        array_unshift($this->requests, [
            'performed_at' => now(),
            'request' => (string)$request
        ]);

        $this->requests = array_slice($this->requests, 0, config('httpdump.max_dumps', 10));
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'requests' => collect($this->requests)->map(function ($requestData) {
                return new LoggedRequest($requestData['performed_at'], $requestData['request']);
            })
        ];
    }
}
