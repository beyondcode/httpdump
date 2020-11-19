<?php

namespace App\Http\Controllers;

use App\Collectors\CollectorContract;
use App\Exceptions\InvalidDumpException;
use Illuminate\Http\Request;

class ClearRequestsController extends Controller
{
    public function __invoke(CollectorContract $collector, $dump)
    {
        try {
            $dump = $collector->getDump($dump);

            $collector->clearRequests($dump);

            return $dump;
        } catch (InvalidDumpException $e) {
            abort(404);
        }
    }
}
