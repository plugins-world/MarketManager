<?php

namespace Plugins\MarketManager\Utilities;

use Illuminate\Database\Eloquent\Model;

class PluginUtility
{
    public static function qualifyUrl(Model $plugin, string $key)
    {
        return StrUtility::qualifyUrl($plugin[$key], $plugin['plugin_host']);
    }
}