<?php

namespace App\Http\Controllers;

use App\Collectors\CollectorContract;
use App\Exceptions\InvalidDumpException;
use Illuminate\Http\Request;

class InspectDumpController extends Controller
{
    public function __invoke(CollectorContract $collector, Request  $request, $dump, $slashData = null)
    {
        try {
            $dump = $collector->getDump($dump);
        } catch (InvalidDumpException $e) {
            abort(404);
        }

        return view('inspect', ['dump' => $dump]);
    }
}
