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
    public static function checkAuth($request)
    {
        return (static::$authUsing ?: function () use ($request) {
            $pluginsCmds = \FresnsCmdWord::all();
            if (array_key_exists('Manager', $pluginsCmds)) {
                $marketManagerCmds = $pluginsCmds['Manager'];

                if (array_key_exists('checkAuth', $marketManagerCmds)) {
                    /** @var \Fresns\CmdWordManager\CmdWordResponse */
                    $resp = \FresnsCmdWord::plugin('Manager')->checkAuth([
                        'request' => $request,
                    ]);

                    return $resp->isSuccessResponse();
                }
            }

            return app()->environment(['local', 'develop']);
        })($request);
    }

    /**
     * Determine if the given request can access the MarketManager dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function checkPluginAuth($request)
    {
        return (static::$pluginAuthUsing ?: function () use ($request) {
            $pluginsCmds = \FresnsCmdWord::all();
            if (array_key_exists('Manager', $pluginsCmds)) {
                $marketManagerCmds = $pluginsCmds['Manager'];

                if (array_key_exists('checkPluginAuth', $marketManagerCmds)) {
                    /** @var \Fresns\CmdWordManager\CmdWordResponse */
                    $resp = \FresnsCmdWord::plugin('Manager')->checkPluginAuth([
                        'request' => $request,
                    ]);

                    return $resp->isSuccessResponse();
                }
            }

            return true;
        })($request);
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