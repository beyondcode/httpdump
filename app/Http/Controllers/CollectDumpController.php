<?php

namespace App\Http\Controllers;

use App\Collectors\CollectorContract;
use App\Events\IncomingRequest;
use App\Exceptions\InvalidDumpException;
use Illuminate\Http\Request;

class CollectDumpController extends Controller
{
    public function __invoke(CollectorContract $collector, Request  $request, string $dump)
    {
        /**
         * We don't want requests larger than the 
         * configured maximum size.
         */
        if (! $this->requestIsValid($request)) {
            return response()->noContent();
        }

        try {
            $dump = $collector->getDump($dump);

            $collector->createRequest($dump, $request);

            event(new IncomingRequest($dump));
        } catch (InvalidDumpException $exception) {
            abort(404);
        }

        return response()->noContent();
    }

    protected function requestIsValid(Request $request): bool
    {
        return strlen((string)$request) < (config('httpdump.max_request_size_in_kb') * 1024);
    }
}
