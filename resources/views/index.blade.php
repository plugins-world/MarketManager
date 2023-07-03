@extends('MarketManager::layouts.master')

@php
use \Plugins\MarketManager\Utilities\PluginUtility;
@endphp

@section('content')
<header class="container-fulid mb-3 border-bottom">
    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <!-- <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
                    <use xlink:href="#bootstrap"></use>
                </svg>
            </a> -->

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
            <li><a href="#" class="nav-link px-2 link-secondary" onclick="top.location = `{{ $configs['system_url'] }}`">回到系统</a></li>
            <li><a href="#" class="nav-link px-2 link-dark">应用中心</a></li>
        </ul>

        <!-- <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
                <input type="search" class="form-control" placeholder="搜索..." aria-label="Search">
            </form> -->

        <!-- <div class="dropdown text-end">
                <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
                </a>
                <ul class="dropdown-menu text-small">
                    <li><a class="dropdown-item" href="#">New project...</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#">Sign out</a></li>
                </ul>
            </div> -->
    </div>
</header>

<div class="container-fulid pb-3">
    <div class="d-grid gap-4" style="grid-template-columns: 2fr 10fr;">
        <div class="bg-light border rounded-3">
            <h3 class="mx-3 py-3">应用中心</h3>

            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-market-manager">管理插件</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" @if(str_contains($configs['market_server_host'], 'packagist.org' )) href="{{ $configs['market_server_host'] }}" target="_blank" @else data-bs-toggle="tab" data-bs-target="#nav-market" @endif>插件市场</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="installBtn" data-bs-target="#installModal" data-bs-toggle="modal" href="#">安装插件</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-market-plugin-page" data-href="{{ $configs['settings_path'] }}" onclick="goToPluginPage(this)">系统设置</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link visually-hidden" id="nav-plugin-page-tab" data-bs-target="#nav-plugin-page" data-bs-toggle="tab" href="#">插件设置</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="installHelp" data-bs-target="#installHelpModal" data-bs-toggle="modal" href="#">怎么快速完成安装？</a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" id="nav-plugin-page-tab" onclick="injectInstallBtn()">在 packagist.org 注入安装按钮</a>
                </li> -->
            </ul>
        </div>

        <div class="tab-content">
            @if(!str_contains($configs['market_server_host'], 'packagist.org'))
            <div class="tab-pane fade" id="nav-market">
                <iframe src="{{ $configs['market_server_host'] }}" referrerpolicy="no-referrer" crossorigin="anonymous"></iframe>
            </div>
            @endif

            <div class="tab-pane fade" id="nav-plugin-page">
                <iframe src="javascript:false;" id="pluginPageIframe" referrerpolicy="no-referrer" crossorigin="anonymous"></iframe>
            </div>

            <div class="tab-pane fade" id="nav-market-plugin-page">
                <iframe id="marketSettingIframe" referrerpolicy="no-referrer" crossorigin="anonymous"></iframe>
            </div>

            <div class="tab-pane fade show active" id="nav-market-manager">
                <div class="bg-white  border rounded-3">
                    <div class="mx-3 py-3">
                        <h3>扩展插件</h3>
                        <p>这里展示的是系统中已安装的所有插件。</p>

                        <nav class="mt-5">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link @if(!\request()->has('status')) active @endif" id="nav-plugin-tab" data-bs-toggle="tab" data-bs-target="#nav-plugin" type="button" role="tab" onclick="window.location.href=`{{\request()->fullUrlWithoutQuery('status')}}`">全部</button>
                                <button class="nav-link @if(\request()->has('status') && \request()->get('status') == 'active') active @endif" id="nav-plugin-enable-tab" data-bs-toggle="tab" data-bs-target="#nav-plugin" type="button" role="tab" onclick="window.location.href=`{{\request()->fullUrlWithQuery(['status' => 'active'])}}`">已启用</button>
                                <button class="nav-link @if(\request()->has('status') && \request()->get('status') == 'inactive') active @endif" id="nav-plugin-enable-tab" data-bs-toggle="tab" data-bs-target="#nav-plugin" type="button" role="tab" onclick="window.location.href=`{{\request()->fullUrlWithQuery(['status' => 'inactive'])}}`">已禁用</button>
                            </div>
                        </nav>
                        <div class="tab-content pt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active p-3" id="nav-plugin" role="tabpanel" aria-labelledby="nav-plugin-tab" tabindex="0">
                                <table class="table">
                                    <thead>
                                        <tr class="table-primary">
                                            <th scope="col">名称</th>
                                            <th scope="col">描述</th>
                                            <th scope="col">开发者</th>
                                            <th scope="col">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($plugins as $plugin)
                                        <tr>
                                            <td>{{ $plugin['name'] }}</td>
                                            <td>{{ Str::limit($plugin['description'] ?? '', 100) }}</td>
                                            <td>{{ $plugin['author'] }}</td>
                                            <td>
                                                @if($plugin['is_enabled'] == false)
                                                <button type="button" data-fskey="{{ $plugin['fskey'] }}" data-action="activate" class="table-row btn-sm btn btn-link text-success">启用</button>
                                                @elseif($plugin['fskey'] !== 'MarketManager')

                                                @if($plugin['is_enabled'] && $plugin['access_path'])
                                                <button type="button" data-fskey="{{ $plugin['fskey'] }}" data-action="setting" data-settings-url="{{ PluginUtility::qualifyUrl($plugin, 'access_path') }}" class="table-row btn-sm btn btn-light">管理</button>
                                                @endif
                                                @if($plugin['is_enabled'] && $plugin['settings_path'])
                                                <button type="button" data-fskey="{{ $plugin['fskey'] }}" data-action="setting" data-settings-url="{{ PluginUtility::qualifyUrl($plugin, 'settings_path') }}" class="table-row btn-sm btn btn-light">设置</button>
                                                @endif

                                                <button type="button" data-fskey="{{ $plugin['fskey'] }}" data-action="deactivate" class="table-row btn-sm btn btn-link text-danger">停用</button>
                                                @endif
                                                @if($plugin['is_enabled'] == false)
                                                <button type="button" data-fskey="{{ $plugin['fskey'] }}" data-action="remove" class="table-row btn-sm btn btn-link text-danger">卸载</button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="installModal" tabindex="-2" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('plugin.install') }}" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="installModalLabel">安装插件</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3 dropdown">
                        <label class="input-group-text">安装方式</label>

                        <button class="btn btn-outline-secondary dropdown-toggle" id="toggleInstallMentod" type="button" data-bs-toggle="dropdown" aria-expanded="false">输入标识名</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="#" data-install-method="plugin_fskey">输入标识名</a></li>
                            <li><a class="dropdown-item" href="#" data-install-method="plugin_package">输入 composer 包</a></li>
                            <li><a class="dropdown-item" href="#" data-install-method="plugin_url">输入插件下载地址</a></li>
                            <li><a class="dropdown-item" href="#" data-install-method="plugin_directory">输入安装目录</a></li>
                            <li><a class="dropdown-item" href="#" data-install-method="plugin_zipball">上传 zip 压缩包</a></li>
                        </ul>

                        <input type="hidden" name="install_type" value="plugin" required class="form-control" style="display: block;">
                        <input type="hidden" name="install_method" value="plugin_fskey" required class="form-control" style="display: block;">

                        <input type="text" name="plugin_fskey" class="form-control" placeholder="请输入插件 fskey" style="display: block;">
                        <input type="text" name="plugin_package" class="form-control" placeholder="请输入composer 包安装信息" style="display: none;">
                        <input type="text" name="plugin_url" class="form-control" placeholder="请输入插件下载地址" style="display: none;">
                        <input type="text" name="plugin_directory" class="form-control" placeholder="请输入插件 fskey 或插件目录的绝对路径" style="display: none;">
                        <input type="file" name="plugin_zipball" class="form-control" placeholder="请选择插件安装包" style="display: none;" accept=".zip">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary install-btn">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="output" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">安装结果</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="$('#installModal').modal('show')" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="spinner-border text-primary" id="outputLoading" role="status">
                    <span class="visually-hidden">正在安装中...</span>
                </div>
                <iframe src="#" id="content" style="width: 100%;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="$('#installModal').modal('show')">取消</button>
                <button type="button" class="btn btn-primary" onclick="window.location.reload()">确认</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="installHelpModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">怎么快速完成安装？</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group mb-3">
                    <li class="list-group-item list-group-item-warning">
                        目前 <code>Laravel</code> 的插件市场还在建设中，在 <a href="https://marketplace.plugins-world.cn/" target="_blank">https://marketplace.plugins-world.cn/</a> 查看已经发布的插件。
                    </li>
                    <li class="list-group-item list-group-item-warning">
                        公开插件的源码请前往仓库查看：<a href="https://github.com/plugins-world/plugins/" target="_blank">https://github.com/plugins-world/plugins/</a>
                    </li>
                </ul>

                <div class="list-group">
                    <div class="list-group-item list-group-item-action" aria-current="true">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">1. 打开插件列表页</h5>
                        </div>
                        <p class="mb-1">点击左侧菜单中的插件市场，查找需要使用的插件。</p>
                    </div>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">2. 复制链接</h5>
                        </div>
                        <p class="mb-1">选择需要的插件，右键复制链接备用。</p>
                    </div>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">3. 安装插件</h5>
                        </div>
                        <p class="mb-1">点击左侧菜单中的安装插件，选择安装方式<code>输入插件下载地址</code>，并在输入框中粘贴上一步的链接。</p>
                    </div>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">4. 开始安装</h5>
                        </div>
                        <p class="mb-1">点击确认按钮，开始安装，并等待安装完成，预计时常 1~5 分钟，取决于您的项目网络环境。</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">知道了</button>
            </div>
        </div>
    </div>
</div>

<script>
    // 安装类型选择
    $('.dropdown .dropdown-item').on('click', $.debounce(500, function(event) {
        const userChoiceInputMethod = $(this).data('install-method');

        $(this).addClass('active').parent().siblings().find('.dropdown-item').removeClass('active');

        const dropdownMenu = $(this).parentsUntil('.dropdown');
        dropdownMenu.siblings('button').text($(this).text())
        $('input[name="install_method"]').val(userChoiceInputMethod);

        const ele = dropdownMenu.parent().find(`input[name="${userChoiceInputMethod}"]`);

        if (ele) {
            $(ele).css('display', 'block').siblings('input').css('display', 'none');
            $(ele).attr('required', true).siblings('input').attr('required', false);
        }
    }));

    // 确认安装
    $(document).on('click', '.install-btn', $.debounce(250, function(event) {
        $('#outputLoading').show();
        $('#output #content').contents().find('body').empty('');
        $('#output .modal-title').text('安装结果');
        $('#installModal').modal('hide');
        $('#output').modal('show');
    }));

    $('#installModal').on('show.bs.modal', function() {
        $('form button[type="submit"] span').remove();
        $('form button[type="submit"]').prop('disabled', false);

        $('input[name="plugin_fskey"]').val('');
        $('input[name="plugin_package"]').val('');
        $('input[name="plugin_directory"]').val('');
        $('input[name="plugin_zipball"]').val('');
    });

    $('#output').on('show.bs.modal', function() {
        $('#output #content').contents().find('html').html('');
        $('#output #content').contents().find('body').html('');
        $('#output #content').height($('#output #content').contents().outerHeight());
        $('#output #content').css({
            'min-height': 0,
            'height': 0,
        });
    });

    // 安装
    $('#installModal form').submit(function(event) {
        event.preventDefault();

        // 减少上传文件
        if ($('input[name="install_method"]').val() !== 'plugin_zipball') {
            $('input[name="plugin_zipball"]').val('');
        }

        $.ajax({
            method: $(this).attr('method'), // post form
            url: $(this).attr('action'),
            data: new FormData($(this)[0]),
            contentType: false,
            processData: false,
            success: function(response) {
                const ansi_up = new AnsiUp;
                const html = ansi_up.ansi_to_html(response);

                $('#installModal').hide();
                $('#outputLoading').hide();
                $('#output #content').contents().find('body').html(`<pre>${html || '安装成功'}</pre>`);
                $('#output #content').height($('#output #content').contents().outerHeight());
            },
            error: function(response) {
                $('#outputLoading').hide();

                let html = "安装失败 <br><br>" + "请检查服务端日志";
                if (response.responseJSON) {
                    html = "安装失败 <br><br>" + response.responseJSON.err_msg;
                } else if (response.responseText) {
                    html = "安装失败 <br><br>" + response.responseText;
                }

                $('#output #content').contents().find('body').html(html);
                $('#output #content').height($('#output #content').contents().outerHeight());
            },
        });
    });

    // 表格操作
    $(document).on('click', 'table button.table-row', $.debounce(500, function(event) {
        event.preventDefault();

        $(this).prepend('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ');
        $(this).prop('disabled', true);

        const action = $(this).data('action');
        const fskey = $(this).data('fskey');

        let data = {};
        data.plugin = fskey;
        switch (action) {
            case 'activate':
                data.is_enabled = 1;
                updatePlugin(data, this);
                break;
            case 'deactivate':
                data.is_enabled = 0;
                updatePlugin(data, this);
                break;
            case 'setting':
                const obj = this;
                $('#pluginPageIframe').attr('src', $(obj).data('settings-url')).on('load', function() {
                    $(obj).find('span').remove();
                    $(obj).prop('disabled', '');

                    $('#nav-market-manager').removeClass('show active');
                    $('#nav-plugin-page-tab').tab('show');
                });
                break;
            case 'remove':
                uninstallPlugin(data, this);
                break;
        }

        return;
    }));

    function goToPluginPage(ele) {
        event.preventDefault();
        const href = $(ele).data('href');
        if (href == '#') {
            return;
        }

        $('#marketSettingIframe').attr('src', href).on('load', () => {
            $(ele).find('span').remove();
            $(ele).prop('disabled', '');

            $('#nav-market-manager').removeClass('show active');
            $('#nav-market-plugin-page-tab').tab('show');
        });
    }

    var updatePlugin = function(data, _this) {
        $.ajax({
            url: "{{ route('plugin.update', []) }}",
            type: "POST",
            data: data,
            success: function(res) {
                $(_this).find('span').remove();
                $(_this).prop('disabled', false);

                console.log(res)
                window.location.reload();
            },
            error: function(err) {
                console.log(err)
            }
        })
    };

    var uninstallPlugin = function(data, _this) {
        $('#output').modal('show');

        $.ajax({
            url: "{{ route('plugin.uninstall', []) }}",
            type: "POST",
            data: data,
            success: function(res) {
                $('#outputLoading').hide();

                $('#output #content').contents().find('body').html(res || '卸载成功');
                $('#output #content').height($('#output #content').contents().outerHeight());
            },
            error: function(err) {
                console.log(err)
                $('#outputLoading').hide();

                let html = "卸载失败 <br><br>" + "请检查服务端日志";
                if (response.responseJSON) {
                    html = "卸载失败 <br><br>" + response.responseJSON.err_msg;
                } else if (response.responseText) {
                    html = "卸载失败 <br><br>" + response.responseText;
                }

                $('#output #content').contents().find('body').html(html);
                $('#output #content').height($('#output #content').contents().outerHeight());
            }
        })
    }
</script>

<script>
    window.addEventListener('message', (e) => {
        let data
        try {
            data = JSON.parse(e.data)
        } catch (e) {
            return
        }

        if (!data.action || !data) {
            return
        }

        switch (data.action.postMessageKey) {
            case 'fresnsInstallExtension':
                // (new bootstrap.Modal('#installModal')).show();

                setTimeout(function() {
                    $('#toggleInstallMentod').text($('.dropdown-menu a[data-install-method="plugin_package"]').text())
                    $('input[name="install_method"]').val('plugin_package')
                    $('input[name="plugin_package"]').val(data.data.fskey)

                    $('input[name="plugin_package"]').css('display', 'block').siblings('input').css('display', 'none')
                    $('input[name="plugin_package"]').attr('required', true).siblings('input').attr('required', false)

                    $('.install-btn').click()
                }, 1000);
                break
        }
    });

    function injectInstallBtn() {
        console.log($('.requireme input'))

        let btnEle = `<span id="installPackageBtn" style="color:red;border:1px solid #ccc;padding:3px 5px;border-radius:3px;" onclick="parent.postMessage(JSON.stringify({action: {postMessageKey: 'fresnsInstallExtension'}, data:{fskey: document.querySelector('.requireme input').value}}), '*')">安装</span>`;

        $('.requireme input').after(btnEle)

        let code = `$('.requireme input').after(\`${btnEle}\`)`

        console.log(`1. 请在 插件市场搜索插件，并进入详情页`)
        console.log(`2. 请通过开发者工具的元素选择器选择插件详情页`)
        console.log(`3. 请在开发者工具的 Console 面板复制以下内容，并粘贴执行`)
        console.log(`4. 请点击详情页中的安装按钮`)
        console.log(code)

        $('#installPackageBtnCodePreview').text(code);
        let clipboard = new ClipboardJS('.copy');
        clipboard.on('success', function(e) {
            $('.toast').find('.toast-body').html('复制成功');
            $('.toast').toast('show');
        });

    }

    $(document).ready(function() {
        injectInstallBtn();
    });
</script>
@endsection