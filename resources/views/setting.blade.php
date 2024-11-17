@extends('MarketManager::layouts.master')

@section('content')
<div class="container-fulid">
    <div class="card mx-auto mt-5" style="width: 75%;">
        <div class="card-body">
            <h1 class="card-title">Market Manage 设置</h1>

            <form id="settingForm" class="row g-3 mt-5" action="{{ route('market-manager.setting') }}" method="post">
                @csrf

                <div class="mb-3 row">
                    <label for="market_server_host" class="col-sm-2 col-form-label">应用市场服务地址</label>
                    <div class="col-sm-8">
                        <input type="text" name="market_server_host" value="{{ old('market_server_host', $configs['market_server_host'] ?? '') }}" class="form-control" id="market_server_host" placeholder="应用市场服务地址，默认为：https://marketplace.plugins-world.cn">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="system_url" class="col-sm-2 col-form-label">系统访问地址</label>
                    <div class="col-sm-8">
                        <input type="text" name="system_url" value="{{ old('system_url', $configs['system_url'] ?? '') }}" class="form-control" id="system_url" placeholder="系统访问地址">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="settings_path" class="col-sm-2 col-form-label">应用管理器设置路径</label>
                    <div class="col-sm-8">
                        <input type="text" name="settings_path" value="{{ old('settings_path', $configs['settings_path'] ?? '') }}" class="form-control" id="settings_path" placeholder="应用管理器设置路径" readonly>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="install_datetime" class="col-sm-2 col-form-label">应用管理器安装时间</label>
                    <div class="col-sm-8">
                        <input type="text" name="install_datetime" value="{{ old('install_datetime', $configs['install_datetime'] ?? '') }}" class="form-control" id="install_datetime" placeholder="应用市场安装时间" readonly>
                    </div>
                </div>

                <div class="mb-3 row visually-hidden">
                    <label for="build_type" class="col-sm-2 col-form-label">build_type</label>
                    <div class="col-sm-8">
                        <input type="hidden" name="build_type" value="{{ old('build_type', $configs['build_type'] ?? '') }}" class="form-control" id="build_type" placeholder="build_type" readonly>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="github_token" class="col-sm-2 col-form-label">GitHub Token</label>
                    <div class="col-sm-8">
                        <input type="text" name="github_token" value="{{ old('github_token', $configs['github_token'] ?? '') }}" class="form-control" id="github_token" placeholder="请输入 GitHub Token，下载私有 GitHub 插件时使用">
                    </div>
                </div>

                <button type="submit" class="col-sm-1 offset-sm-2 btn btn-primary mb-3">保存</button>
            </form>
        </div>
    </div>
</div>
@endsection