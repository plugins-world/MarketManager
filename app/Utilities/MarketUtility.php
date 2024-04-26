<?php

namespace Plugins\MarketManager\Utilities;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Plugins\LaravelConfig\Helpers\ConfigHelper;

class MarketUtility
{
    const version = '1.0.0';
    const versionInt = 1;
    const defaultMarketHost = 'https://marketplace.plugins-world.cn';

    public static function currentVersion()
    {
        return [
            'version' => static::version,
            'versionInt' => static::versionInt,
        ];
    }

    public static function macroMarketHeaders()
    {
        Http::macro('market', function () {
            $httpProxy = config('app.http_proxy');
            $http = Http::baseUrl(MarketUtility::getApiHost())
                ->withHeaders(MarketUtility::getMarketHeaders())
                ->withOptions([
                    'proxy' => [
                        'http' => $httpProxy,
                        'https' => $httpProxy,
                    ],
                ]);

            return $http;
        });
    }

    public static function getApiHost()
    {
        $apiHost = ConfigHelper::fresnsConfigByItemKey('market_server_host');

        if (!$apiHost) {
            return static::defaultMarketHost;
        }

        return $apiHost;
    }

    public static function getMarketHeaders(): array
    {
        $appConfig = ConfigHelper::fresnsConfigByItemKeys([
            'install_datetime',
            'build_type',
            'site_url',
            'site_name',
            'site_desc',
            'site_copyright',
            'default_timezone',
            'default_language',
        ]);

        $isHttps = \request()->getScheme() === 'https';

        $systemUrl = $appConfig['system_url'] ?? config('app.url');

        config([
            'app.url' => trim($systemUrl, '/'),
        ]);

        return [
            'panelLangTag' => App::getLocale(),
            'installDatetime' => $appConfig['install_datetime'],
            'buildType' => $appConfig['build_type'],
            'version' => static::currentVersion()['version'],
            'versionInt' => static::currentVersion()['versionInt'],
            'httpSsl' => $isHttps ? 1 : 0,
            'httpHost' => \request()->getHost(),
            'httpPort' => \request()->getPort(),
            'systemUrl' => config('app.url'),
            'siteUrl' => $appConfig['site_url'],
            'siteName' => base64_encode($appConfig['site_name']),
            'siteDesc' => base64_encode($appConfig['site_desc']),
            'siteCopyright' => base64_encode($appConfig['site_copyright']),
            'siteTimezone' => $appConfig['default_timezone'],
            'siteLanguage' => $appConfig['default_language'],
        ];
    }
}
