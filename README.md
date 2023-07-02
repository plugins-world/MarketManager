# MarketManager

[![Latest Stable Version](http://poser.pugx.org/plugins-world/market-manager/v)](https://packagist.org/packages/plugins-world/market-manager)
[![Total Downloads](http://poser.pugx.org/plugins-world/market-manager/downloads)](https://packagist.org/packages/plugins-world/market-manager)
[![Latest Unstable Version](http://poser.pugx.org/plugins-world/market-manager/v/unstable)](https://packagist.org/packages/plugins-world/market-manager) [![License](http://poser.pugx.org/plugins-world/market-manager/license)](https://packagist.org/packages/plugins-world/market-manager)
[![PHP Version Require](http://poser.pugx.org/plugins-world/market-manager/require/php)](https://packagist.org/packages/plugins-world/market-manager)


## 安装

你可以通过 composer 安装这个扩展包，与应用插件不同的是，此扩展会安装到 `vendor/` 目录下

⚠️注意：需要确保项目 `composer.json` 允许安装稳定性依赖为 `dev` 的扩展包
```
{
    ...
    "minimum-stability": "dev",
    "prefer-stable": true,
    ...
}
```

1. 初始化
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

3. 访问路由：`/market-manager`

4. 限制访问授权
默认只允许 `local` 与 `develop` 环境访问。

如果需要限制访问权限，可以在 `app/Providers/AppServiceProvider.php` 的 `boot` 函数中，通过指定 MarketManager 如何进行认证来完成限制，参考如下：

- 通过 `AppServiceProvider` 授权
```
\Plugins\MarketManager\MarketManager::auth(function ($request) {
    // return true / false;
});
```

- 通过命令字 `\FresnsCmdWord::plugin('Manager')->auth([])` 授权，需要自行实现 `Manager` 的 `auth` 命令字。


**注意，安装的时候，会询问是以下内容，请输入： `y`**
> wikimedia/composer-merge-plugin contains a Composer plugin which is currently not in your allow-plugins config. See https://getcomposer.org/allow-plugins  
> 
> Do you trust "wikimedia/composer-merge-plugin" to execute code and wish to enable it now? (writes "allow-plugins" to composer.json) [y,n,d,?]



## 注意

1. 应用市场：目前还没有发布官方的 Laravel 应用市场，你可以将应用市场的地址配置为 `https://packagist.plugins-world.cn/` 进行使用。当前默认采用 `https://packagist.org` 作为应用市场。
2. 目前，https://github.com/plugins-world/plugins 下的插件都可以作为插件进行安装。请注意插件间可能存在冲突。
3. 项目需要配置好权限，避免 web 程序的用户无法读取、创建目录。
4. 每次安装后，插件默认关闭，需要进行启用操作。
5. 如希望增加是否可以访问应用市场的验证，请耐心等待下一版本迭代。


## 遇到问题

通过此处联系我：https://plugins-world.cn/contributing/feedback.html
