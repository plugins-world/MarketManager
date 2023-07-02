<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\MarketManager\Providers;

use Illuminate\Support\Facades\Route;
use Plugins\MarketManager\Utilities\MarketUtility;
use Plugins\MarketManager\Http\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class MarketManagerServiceProvider extends BaseServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__, 2) . '/resources/assets/' => public_path('assets/plugins/MarketManager'),
        ]);

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->loadMigrationsFrom(dirname(__DIR__, 2) . '/database/migrations');

        Route::aliasMiddleware('market-manager.auth', Authenticate::class);
        $this->app->register(RouteServiceProvider::class);

        MarketUtility::macroMarketHeaders();

        // Event::listen(UserCreated::class, UserCreatedListener::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // if ($this->app->runningInConsole()) {
            $this->app->register(CommandServiceProvider::class);
        // }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 2) . '/config/market-manager.php', 'market-manager'
        );

        config([
            'plugins.composer' => config('market-manager.composer'),
        ]);
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(dirname(__DIR__, 2) . '/resources/views', 'MarketManager');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(dirname(__DIR__, 2) . '/resources/lang', 'MarketManager');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
