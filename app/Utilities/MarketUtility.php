<?php

namespace Plugins\MarketManager\Utilities;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use MouYong\LaravelConfig\Models\Config;

class MarketUtility
{
    const version = '1.0.0';
    const versionInt = 1;
    
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
            return Http::withHeaders(
                MarketUtility::getMarketHeaders()
            )->baseUrl(
                MarketUtility::getApiHost()
            );
        });
    }

    public static function getApiHost(): string
    {
        $apiHost = Config::getValueByKey('market_server_host');

        return $apiHost;
    }

    public static function getMarketHeaders(): array
    {
        $appConfig = Config::getValueByKeys([
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
            'version' => self::currentVersion()['version'],
            'versionInt' => self::currentVersion()['versionInt'],
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