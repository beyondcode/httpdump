<?php

namespace App\Http\Controllers;

use App\Collectors\CollectorContract;
use Illuminate\Http\Request;

class CreateDumpController extends Controller
{
    public function __invoke(CollectorContract $collector)
    {
        $dump = $collector->createDump();

        return redirect()->route('inspect', [$dump->name]);
    }
}
