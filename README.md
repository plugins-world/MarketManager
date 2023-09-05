# 插件管理器

[![Latest Stable Version](http://poser.pugx.org/plugins-world/market-manager/v)](https://packagist.org/packages/plugins-world/market-manager)
[![Total Downloads](http://poser.pugx.org/plugins-world/market-manager/downloads)](https://packagist.org/packages/plugins-world/market-manager)
[![Latest Unstable Version](http://poser.pugx.org/plugins-world/market-manager/v/unstable)](https://packagist.org/packages/plugins-world/market-manager) [![License](http://poser.pugx.org/plugins-world/market-manager/license)](https://packagist.org/packages/plugins-world/market-manager)
[![PHP Version Require](http://poser.pugx.org/plugins-world/market-manager/require/php)](https://packagist.org/packages/plugins-world/market-manager)


## 安装

你可以通过 composer 安装这个扩展包，与应用插件不同的是，此扩展会安装到 `vendor/` 目录下。

⚠️注意，安装的时候，会询问是以下内容，请输入： `y`
> wikimedia/composer-merge-plugin contains a Composer plugin which is currently not in your allow-plugins config. See https://getcomposer.org/allow-plugins  
> 
> Do you trust "wikimedia/composer-merge-plugin" to execute code and wish to enable it now? (writes "allow-plugins" to composer.json) [y,n,d,?]


下面是初始化项目并引入插件管理器的操作步骤：

1. 创建项目
```bash
# 创建新项目 laravel-test
composer create-project --prefer-dist laravel/laravel laravel-test
# 进入项目目录
cd laravel-test
# 初始化 git 仓库
git init
git add .
git commit -m "feat: Init."
# 配置应用市场管理器, 插件管理器, 命令字管理器的安装源（此步骤仅在需要最新管理器功能时配置）
# composer config repositories.plugin-manager vcs https://gitee.com/fresns/plugin-manager
# composer config repositories.market-manager vcs https://gitee.com/fresns/market-manager
# composer config repositories.cmd-word-manager vcs https://gitee.com/fresns/cmd-word-manager
```

2. 修改依赖包约束
⚠️注意：需要确保项目 `composer.json` 允许安装稳定性依赖为 `dev` 的扩展包
```
{
    ...
    "minimum-stability": "dev",
    "prefer-stable": true,
    ...
}
```

3. 安装插件管理器，并完成初始化。
```bash
# 安装 Laravel 的应用市场管理器
composer require plugins-world/market-manager
# 配置 .env 中的数据库与项目信息
	APP_NAME=
	APP_URL=

	DB_HOST=
	DB_DATABASE=
	DB_USERNAME=
	DB_PASSWORD=

# 执行迁移，增加 plugins 表
php artisan migrate

# 提交仓库变动。方便查看 saas 初始化的文件
git add .
git commit -m "feat: Install laravel market-manager."
```

4. 正确配置项目权限  
宝塔：`chown www:www -R /path/to/laravel-test`

5. 访问：`http://域名/market-manager`，查看安装结果


## 挑选插件

访问 `http://域名/market-manager`，打开左侧的插件市场菜单，并在其中查找需要的插件。进入插件详情页后点击安装。

⚠️注意：独立打开插件市场，不会显示安装按钮。


## 插件管理页的访问限制

⚠️注意：
- MarketManager 默认只允许 `local` 与 `develop` 环境访问。
- Plugin 默认全部放行访问。
- 如果需要限制访问权限，可以在 `app/Providers/AppServiceProvider.php` 的 `boot` 函数中，通过指定 MarketManager 如何进行认证来完成限制，参考如下：


操作步骤：
1. 安装 SanctumAuth 插件
2. 通过 artisan 命令 app:user-add 创建一个初始账号
3. 正确配置主程序。下面是配置参考

- 通过 `AppServiceProvider` 授权
```
\Plugins\MarketManager\MarketManager::auth(function ($request, $next) {
    // return \Illuminate\Support\Facades\Auth::onceBasic() ?: $next($request);
});


\Plugins\MarketManager\MarketManager::pluginAuth(function ($request, $next) {
    // return \Illuminate\Support\Facades\Auth::onceBasic() ?: $next($request);
});

# 配置首页默认路由，并进行 basic 认证（需要在数据库创建 users 信息，通过 email, password 登录）
Route::domain(parse_url(config('app.url'), PHP_URL_HOST))->get('/', function () {
    return redirect('/market-manager');
    return view('welcome');
})->middleware('auth.basic');
```

- 通过命令字 `\FresnsCmdWord::plugin('Manager')->checkAuth([])` 授权 MarketManager 访问，需要自行实现 `Manager` 的 `checkAuth` 命令字。
- 通过命令字 `\FresnsCmdWord::plugin('Manager')->checkPluginAuth([])` 授权 Plugin 访问，需要自行实现 `Manager` 的 `checkPluginAuth` 命令字。


## 说明

1. 应用市场：https://marketplace.plugins-world.cn
2. 符合插件管理器开发规范的插件可以被安装
3. 插件安装方式：
   1. 从 url 安装 zip 插件
   2. 从 github url 安装私有插件
   3. 从 github url 安装公开插件
   4. 从 下载站获取 url 安装插件：https://apps.plugins-world.cn
   5. 从 https://packagist.org 通过安命令完成插件安装
   6. 上传 zip 插件到服务器进行安装
   7. 从指定目录安装
   8. 从插件市场安装插件（开发中）
4. 目前，官方插件代码仓库为：https://github.com/plugins-world/plugins
5. 项目需要配置好权限，避免插件无法下载，解压，安装。插件的安装需要 web 程序的用户读取、创建目录。
6. 每次安装后，插件默认关闭，需要进行启用操作。


## 遇到问题

通过此处联系我：https://plugins-world.cn/contributing/feedback.html
