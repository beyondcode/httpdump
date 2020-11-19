<?php

namespace App\Collectors;

use App\Models\Dump;
use Illuminate\Http\Request;

interface CollectorContract
{
    public function createDump(): Dump;

    public function createRequest(Dump $dump, Request  $request);

    public function countDumps(): int;

    public function countRequests(): int;

    public function clearRequests(Dump $dump);

    public function getDump(string $name): Dump;
}
