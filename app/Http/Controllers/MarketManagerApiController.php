<?php

namespace Plugins\MarketManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Plugins\LaravelConfig\Models\Config;

class MarketManagerApiController extends Controller
{
    public function __construct()
    {
        if(!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'rb'));
        if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
        if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));
    }

    public function install()
    {
        \request()->validate([
            'install_type' => 'nullable', // plugin, theme
            'install_method' => 'required|in:plugin_fskey,plugin_package,plugin_url,plugin_directory,plugin_zipball',

            'plugin_fskey' => 'required_if:install_method,plugin_fskey',
            'plugin_package' => 'required_if:install_method,plugin_package',
            'plugin_url' => 'required_if:install_method,plugin_url',
            'plugin_directory' => 'required_if:install_method,plugin_directory',
            'plugin_zipball' => 'required_if:install_method,plugin_zipball',
        ]);

        $install_type = \request('install_type', 'plugin');
        $install_method = \request('install_method');
        $installValue = \request($install_method);

        switch ($install_method) {
            // fskey
            case 'plugin_fskey':
            case 'plugin_package':
            case 'plugin_url':
                if ($install_method == 'plugin_url') {
                    $configs = Config::getValueByKeys([
                        'github_token',
                    ]);

                    // 下载 github 私有插件
                    if (str_starts_with($installValue, 'https://github.com')) {
                        $installValue = str_replace('https://', 'https://' . $configs['github_token'] . ':@', $installValue);

                        if (! str_ends_with($installValue, 'zip')) {
                            $installValue = $installValue . '/archive/master.zip';
                        }
                    }
                } 

                // market-manager
                $exitCode = Artisan::call('market:require', [
                    'fskey' => $installValue,
                ]);
                $output = Artisan::output();
            break;

            // directory
            case 'plugin_directory':
                $pluginDirectory = $installValue;

                // plugin-manager or theme-manager
                $exitCode = Artisan::call("{$install_type}:install", [
                    'path' => $pluginDirectory,
                    '--is_dir' => true,
                ]);
                $output = Artisan::output();
            break;

            // plugin_zipball
            case 'plugin_zipball':
                $pluginZipball = null;
                $file = $installValue;

                if ($file && $file->isValid()) {
                    $dir = storage_path('extensions');
                    $filename = $file->hashName();
                    $file->move($dir, $filename);

                    $pluginZipball = "$dir/$filename";
                }

                if (empty($pluginZipball)) {
                    return $this->fail('插件安装失败，请选择插件压缩包');
                }

                // plugin-manager or theme-manager
                $exitCode = Artisan::call("{$install_type}:install", [
                    'path' => $pluginZipball,
                ]);
                $output = Artisan::output();
            break;
        }

        if ($exitCode != 0) {
            if ($output == '') {
                $output = "请查看安装日志 storage/logs/laravel.log";
            }

            return \response($output."\n 安装失败");
        }

        return \response($output."\n 安装成功");
    }

    public function update()
    {
        \request()->validate([
            'plugin' => 'required|string',
            'is_enabled' => 'required|boolean'
        ]);

        $fskey = \request('plugin');
        if (\request()->get('is_enabled') != 0) {
            $exitCode = Artisan::call('market:activate', ['fskey' => $fskey]);
        } else {
            $exitCode = Artisan::call('market:deactivate', ['fskey' => $fskey]);
        }

        if ($exitCode !== 0) {
            return $this->fail(Artisan::output());
        }

        return \response()->json([
            'err_code' => 0,
            'err_msg' => 'success',
            'data' => null,
        ]);
    }

    public function uninstall()
    {
        \request()->validate([
            'plugin' => 'required|string',
            'clearData' => 'nullable|bool',
        ]);

        $fskey = \request('plugin');
        if (\request()->get('clearData') == 1) {
            $exitCode = Artisan::call('market:remove-plugin', ['fskey' => $fskey, '--cleardata' => true]);
        } else {
            $exitCode = Artisan::call('market:remove-plugin', ['fskey' => $fskey, '--cleardata' => false]);
        }

        $message = '卸载成功';
        if ($exitCode != 0) {
            $message = Artisan::output()."\n卸载失败";
        }

        return \response($message);
    }

}
