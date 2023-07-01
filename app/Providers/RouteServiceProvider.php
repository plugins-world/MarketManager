<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\MarketManager\Providers;

use Doctrine\Common\Cache\Cache;
use Illuminate\Support\Facades\Route;
use Fresns\MarketManager\Models\Plugin;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseServiceProvider;

class RouteServiceProvider extends BaseServiceProvider
{
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $host = null;

        // try {
        //     if (class_exists(Plugin::class)) {
        //         $fskey = 'MarketManager';
        //         $cacheKey = "MarketManager_model";

        //         $pluginModel = Cache::get($cacheKey);
        //         if (empty($pluginModel)) {
        //             $pluginModel = Plugin::withTrashed()->where('fskey', $fskey)->first();

        //             Cache::put($cacheKey, $pluginModel, now()->addMinutes(30));
        //         }

        //         $pluginHost = $pluginModel?->plugin_host ?? '';

        //         $host = str_replace(['http://', 'https://'], '', rtrim($pluginHost, '/'));
        //     }
        // } catch (\Throwable $e) {
        //     info("get plugin host failed: " . $e->getMessage());
        // }

        Route::group([
            'domain' => $host,
        ], function () {
            $this->mapApiRoutes();

            $this->mapWebRoutes();
        });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->group(dirname(__DIR__, 2) . '/routes/web.php');
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(dirname(__DIR__, 2) . '/routes/api.php');
    }
}
