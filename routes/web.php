<?php

use App\Http\Controllers\CollectDumpController;
use App\Http\Controllers\CreateDumpController;
use App\Http\Controllers\InspectDumpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/create-dump', CreateDumpController::class);
Route::any('/dumps/{dump}/{slashData?}', CollectDumpController::class)->where('slashData', '(.*)')->name('collect');
Route::get('/inspect/{dump}', InspectDumpController::class)->name('inspect');
