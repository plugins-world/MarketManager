<?php

namespace Plugins\MarketManager;

use Closure;

class MarketManager
{
    /**
     * The callback that should be used to authenticate Horizon users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * Determine if the given request can access the Horizon dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function check($request)
    {
        return (static::$authUsing ?: function () use ($request) {
            $pluginsCmds = \FresnsCmdWord::all();
            if (array_key_exists('Manager', $pluginsCmds)) {
                $marketManagerCmds = $pluginsCmds['Manager'];

                if (array_key_exists('auth', $marketManagerCmds)) {
                    /** @var \Fresns\CmdWordManager\CmdWordResponse */
                    $resp = \FresnsCmdWord::plugin('Manager')->auth([
                        'request' => $request,
                    ]);

                    return $resp->isSuccessResponse();
                }
            }

            return app()->environment(['local', 'develop']);
        })($request);
    }

    /**
     * Set the callback that should be used to authenticate Horizon users.
     *
     * @param  \Closure  $callback
     * @return static
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }
}