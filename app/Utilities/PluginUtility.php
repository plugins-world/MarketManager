<?php

namespace Plugins\MarketManager\Utilities;

use Fresns\MarketManager\Models\Plugin;
use Illuminate\Database\Eloquent\Model;
use Plugins\MarketManager\Utils\LaravelCache;

class PluginUtility
{
    public static function qualifyUrl(Model $plugin, string $key)
    {
        return StrUtility::qualifyUrl($plugin[$key], $plugin['plugin_host']);
    }

    // Get the plugin host.
    public static function fresnsPluginHostByFskey(?string $fskey): ?string
    {
        if (empty($fskey)) {
            return null;
        }

        $cacheKey = "fresns_plugin_host_{$fskey}";

        return LaravelCache::remember($cacheKey, function () use ($fskey) {
            return Plugin::where('fskey', $fskey)->value('plugin_host');
        });
    }

    // Get the plugin access url
    public static function fresnsPluginUrlByFskey(?string $fskey = null): ?string
    {
        if (empty($fskey)) {
            return null;
        }

        $cacheKey = "fresns_plugin_url_{$fskey}";

        return LaravelCache::remember($cacheKey, function () use ($fskey) {
            $plugin = Plugin::where('fskey', $fskey)->first();

            $pluginUrl = null;
            if ($plugin) {
                $url = empty($plugin->plugin_host) ? config('app.url') : $plugin->plugin_host;

                $pluginUrl = StrUtility::qualifyUrl($plugin->access_path, $url);
            }

            return $pluginUrl;
        });
    }

    // Get the url of the plugin that has replaced the custom parameters
    public static function fresnsPluginUsageUrl(string $fskey, ?string $parameter = null): ?string
    {
        $url = PluginUtility::fresnsPluginUrlByFskey($fskey);

        if (empty($parameter) || empty($url)) {
            return $url;
        }

        return str_replace('{parameter}', $parameter, $url);
    }

    // get plugin callback
    public static function fresnsPluginCallback(string $fskey, string $ulid): array
    {
        $callbackArr = [
            'code' => 0,
            'data' => [],
        ];

        $plugin = Plugin::where('fskey', $fskey)->first();

        if (empty($plugin)) {
            $callbackArr['code'] = 32101;

            return $callbackArr;
        }

        if (! $plugin->is_enabled) {
            $callbackArr['code'] = 32102;

            return $callbackArr;
        }

        $callback = PluginCallback::where('ulid', $ulid)->first();

        if (empty($callback)) {
            $callbackArr['code'] = 32303;

            return $callbackArr;
        }

        if ($callback->is_used) {
            $callbackArr['code'] = 32204;

            return $callbackArr;
        }

        if (empty($callback->content)) {
            $callbackArr['code'] = 32206;

            return $callbackArr;
        }

        $timeDifference = time() - strtotime($callback->created_at);
        // 30 minutes
        if ($timeDifference > 1800) {
            $callbackArr['code'] = 32203;

            return $callbackArr;
        }

        $callback->is_used = 1;
        $callback->used_plugin_fskey = $fskey;
        $callback->save();

        $data = [
            'ulid' => $callback->ulid,
            'type' => $callback->type,
            'content' => $callback->content,
        ];

        $callbackArr['data'] = $data;

        return $callbackArr;
    }

    // get plugin version
    public static function fresnsPluginVersionByFskey(string $fskey): ?string
    {
        $cacheKey = "fresns_plugin_version_{$fskey}";

        $version = LaravelCache::remember($cacheKey, function () use ($fskey) {
            return Plugin::where('fskey', $fskey)->value('version');
        });

        return $version;
    }

    // get plugin upgrade code
    public static function fresnsPluginUpgradeCodeByFskey(string $fskey): ?string
    {
        $upgradeCode = Plugin::where('fskey', $fskey)->value('upgrade_code');

        if (empty($upgradeCode)) {
            return null;
        }

        return $upgradeCode;
    }
}