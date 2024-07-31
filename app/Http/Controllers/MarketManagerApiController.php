<?php

namespace Plugins\MarketManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Plugins\PhpSupport\Traits\ResponseTrait;
use Plugins\LaravelConfig\Helpers\ConfigHelper;
use Plugins\MarketManager\Models\App;

class MarketManagerApiController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        if (!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'rb'));
        if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
        if (!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));
    }

    public function install()
    {
        \request()->validate([
            'install_type' => 'nullable', // plugin, theme
            'install_method' => 'required|in:app_fskey,app_package,app_url,app_directory,app_zipball',

            'app_fskey' => 'required_if:install_method,app_fskey',
            'app_package' => 'required_if:install_method,app_package',
            'app_url' => 'required_if:install_method,app_url',
            'app_directory' => 'required_if:install_method,app_directory',
            'app_zipball' => 'required_if:install_method,app_zipball',
        ]);

        $install_type = \request('install_type', 'plugin');
        $install_method = \request('install_method');
        $installValue = \request($install_method);

        switch ($install_method) {
                // fskey
            case 'app_fskey':
            case 'app_package':
            case 'app_url':
                if ($install_method == 'app_url') {
                    $configs = ConfigHelper::fresnsConfigByItemKeys([
                        'github_token',
                    ]);

                    // 下载 github 私有插件
                    if (str_starts_with($installValue, 'https://github.com')) {
                        $installValue = str_replace('https://', 'https://' . $configs['github_token'] . ':@', $installValue);

                        if (!str_ends_with($installValue, 'zip')) {
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
            case 'app_directory':
                $pluginDirectory = $installValue;

                // plugin-manager or theme-manager
                $exitCode = Artisan::call("{$install_type}:install", [
                    'path' => $pluginDirectory,
                    '--is_dir' => true,
                ]);
                $output = Artisan::output();
                break;

                // app_zipball
            case 'app_zipball':
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

            return \response($output . "\n 安装失败");
        }

        return \response($output . "\n 安装成功");
    }

    public function update()
    {
        \request()->validate([
            'plugin' => 'required|string',
            'is_enabled' => 'required|boolean'
        ]);

        $fskey = \request('plugin');

        if (\request()->get('is_enabled') != 0) {
            $exitCode = Artisan::call('plugin:activate', ['fskey' => $fskey]);
        } else {
            $exitCode = Artisan::call('plugin:deactivate', ['fskey' => $fskey]);
        }

        if ($exitCode !== 0) {
            return $this->fail(Artisan::output());
        }

        $app = App::where('fskey', $fskey)->first();
        if ($app) {
            $app->update([
                'is_enabled' => !$app->is_enabled,
            ]);
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
            $exitCode = Artisan::call('plugin:uninstall', ['fskey' => $fskey, '--cleardata' => true]);
        } else {
            $exitCode = Artisan::call('plugin:uninstall', ['fskey' => $fskey, '--cleardata' => false]);
        }

        if ($exitCode == 0) {
            App::where('fskey', $fskey)->delete();
        }

        $message = '卸载成功';
        if ($exitCode != 0) {
            $message = Artisan::output() . "\n卸载失败";
        }

        return \response($message);
    }
}
