<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\MarketManager\Http\Controllers as WebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('market-manager')->group(function() {
    Route::get('/', [WebController\MarketManagerController::class, 'index'])->name('market-manager.index');
    Route::get('setting', [WebController\MarketManagerController::class, 'showSettingView'])->name('market-manager.setting');
    Route::post('setting', [WebController\MarketManagerController::class, 'saveSetting']);
});

// without VerifyCsrfToken
// Route::prefix('market-manager')->withoutMiddleware([
//     \App\Http\Middleware\EncryptCookies::class,
//     \App\Http\Middleware\VerifyCsrfToken::class,
// ])->group(function() {
//     Route::get('/', [WebController\MarketManagerController::class, 'index']);
// });