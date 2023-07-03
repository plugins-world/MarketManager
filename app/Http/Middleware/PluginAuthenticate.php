<?php

namespace Plugins\MarketManager\Http\Middleware;

use Plugins\MarketManager\MarketManager;

class PluginAuthenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|null
     */
    public function handle($request, $next)
    {
        return MarketManager::checkPluginAuth($request) ? $next($request) : abort(403);
    }
}