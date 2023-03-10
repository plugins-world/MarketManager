<?php

namespace Plugins\MarketManager\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Fresns\MarketManager\Models\Plugin;
use MouYong\LaravelConfig\Models\Config;

class MarketManagerController extends Controller
{
    public function index()
    {
        $configs = Config::getValueByKeys([
            'market_server_host',
            'system_url',
            'settings_path',
            'install_datetime',
            'build_type',
        ]);

        $where = [];
        if (\request()->has('is_enable')) {
            $where['is_enable'] = \request('is_enable');
        }

        $plugins = Plugin::query()->where($where)->get();

        return view('MarketManager::index', [
            'configs' => $configs,
            'plugins' => $plugins,
        ]);
    }

    public function showSettingView()
    {
        $configs = Config::getValueByKeys([
            'market_server_host',
            'system_url',
            'settings_path',
            'install_datetime',
            'build_type',
        ]);

        return view('MarketManager::setting', [
            'configs' => $configs,
        ]);
    }

    public function saveSetting()
    {
        \request()->validate([
            'market_server_host' => 'required|url',
            'system_url' => 'nullable|url',
            'settings_path' => 'required|string',
        ]);

        $itemKeys = [
            'market_server_host',
            'system_url',
            'settings_path',
        ];

        Config::updateConfigs($itemKeys, 'market_manager');

        return redirect(route('market-manager.setting'));
    }
}
