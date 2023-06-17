<?php

namespace Plugins\MarketManager\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Fresns\MarketManager\Models\Plugin;
use Plugins\LaravelConfig\Models\Config;

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
            'github_token',
        ]);

        $where = [];
        if (\request()->has('status')) { // 1-active, 0-inactive
            $where['is_enabled'] = \request('status') == 'active' ? 1 : 0;
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
            'github_token',
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
            'github_token' => 'nullable|string',
        ]);

        $itemKeys = [
            'market_server_host',
            'system_url',
            'settings_path',
            'github_token',
        ];

        Config::updateConfigs($itemKeys, 'market_manager');

        return redirect(route('market-manager.setting'));
    }
}
