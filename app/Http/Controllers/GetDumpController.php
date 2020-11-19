<?php

namespace App\Http\Controllers;

use App\Collectors\CollectorContract;
use App\Exceptions\InvalidDumpException;
use Illuminate\Http\Request;

class GetDumpController extends Controller
{
    public function __invoke(CollectorContract $collector, $dump)
    {
        try {
            return $collector->getDump($dump);
        } catch (InvalidDumpException $e) {
            abort(404);
        }
    }
}
