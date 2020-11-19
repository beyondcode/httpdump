<?php

use App\Http\Controllers\ClearRequestsController;
use App\Http\Controllers\GetDumpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/dumps/{dump}', GetDumpController::class);
Route::get('/requests/clear/{dump}', ClearRequestsController::class);
