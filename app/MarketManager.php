<?php

namespace Plugins\MarketManager;

use Closure;

class MarketManager
{
    /**
     * The callback that should be used to authenticate MarketManager users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * The callback that should be used to authenticate Plugin users.
     *
     * @var \Closure
     */
    public static $pluginAuthUsing;

    /**
     * Determine if the given request can access the MarketManager dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function checkAuth($request, $next)
    {
        return (static::$authUsing ?: function ($request, $next) {
            $pluginsCmds = \FresnsCmdWord::all();
            if (array_key_exists('Manager', $pluginsCmds)) {
                $marketManagerCmds = $pluginsCmds['Manager'];

                if (array_key_exists('checkAuth', $marketManagerCmds)) {
                    /** @var \Fresns\CmdWordManager\CmdWordResponse */
                    $resp = \FresnsCmdWord::plugin('Manager')->checkAuth([
                        'request' => $request,
                        'next' => $next,
                    ]);

                    return $resp->isSuccessResponse();
                }
            }

            if (app()->environment(['local', 'develop'])) {
                return $next($request);
            }

            return abort(403);
        })($request, $next);
    }

    /**
     * Determine if the given request can access the MarketManager dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function checkPluginAuth($request, $next)
    {
        return (static::$pluginAuthUsing ?: function ($request, $next) {
            $pluginsCmds = \FresnsCmdWord::all();
            if (array_key_exists('Manager', $pluginsCmds)) {
                $marketManagerCmds = $pluginsCmds['Manager'];

                if (array_key_exists('checkPluginAuth', $marketManagerCmds)) {
                    /** @var \Fresns\CmdWordManager\CmdWordResponse */
                    $resp = \FresnsCmdWord::plugin('Manager')->checkPluginAuth([
                        'request' => $request,
                        'next' => $next,
                    ]);

                    return $resp->isSuccessResponse();
                }
            }

            return $next($request);
        })($request, $next);
    }

    /**
     * Set the callback that should be used to authenticate MarketManager users.
     *
     * @param  \Closure  $callback
     * @return static
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }

    /**
     * Set the callback that should be used to authenticate MarketManager users.
     *
     * @param  \Closure  $callback
     * @return static
     */
    public static function pluginAuth(Closure $callback)
    {
        static::$pluginAuthUsing = $callback;

        return new static;
    }
}