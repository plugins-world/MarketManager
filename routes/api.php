<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Plugins\MarketManager\Http\Controllers as ApiController;

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

// Route::middleware('auth:api')->get('/market-manager', function (Request $request) {
//     return $request->user();
// });

Route::prefix('market-manager')->group(function() {
    Route::post('/', [ApiController\MarketManagerApiController::class, 'install'])->name('app.install');
    Route::post('app-update', [ApiController\MarketManagerApiController::class, 'update'])->name('app.update');
    Route::post('app-uninstall', [ApiController\MarketManagerApiController::class, 'uninstall'])->name('app.uninstall');
});
