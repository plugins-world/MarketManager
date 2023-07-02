<?php

namespace Plugins\MarketManager\Http\Middleware;

use Plugins\MarketManager\MarketManager;

class Authenticate
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
        return MarketManager::check($request) ? $next($request) : abort(403);
    }
}