@extends('MarketManager::layouts.master')

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
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-market">插件市场</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#installModal" data-bs-toggle="modal" href="#">安装插件</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-market-plugin-page" data-href="{{ $configs['settings_path'] }}" onclick="goToPluginPage(this)">系统设置</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link visually-hidden" id="nav-plugin-page-tab" data-bs-target="#nav-plugin-page" data-bs-toggle="tab" href="#">插件设置</a>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade" id="nav-market">
                <iframe src="{{ $configs['market_server_host'] }}"></iframe>
            </div>

            <div class="tab-pane fade" id="nav-plugin-page">
                <iframe src="javascript:false;" id="pluginPageIframe"></iframe>
            </div>

            <div class="tab-pane fade" id="nav-market-plugin-page">
                <iframe id="marketSettingIframe"></iframe>
            </div>

            <div class="tab-pane fade show active" id="nav-market-manager">
                <div class="bg-white  border rounded-3">
                    <div class="mx-3 py-3">
                        <h3>扩展插件</h3>
                        <p>这里展示的是系统中已安装的所有插件。</p>

                        <nav class="mt-5">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link @if(!\request()->has('is_enable')) active @endif" id="nav-plugin-tab" data-bs-toggle="tab" data-bs-target="#nav-plugin" type="button" role="tab" onclick="window.location.href=`{{\request()->fullUrlWithoutQuery('is_enable')}}`">全部</button>
                                <button class="nav-link @if(\request()->has('is_enable') && \request()->get('is_enable') == 1) active @endif" id="nav-plugin-enable-tab" data-bs-toggle="tab" data-bs-target="#nav-plugin" type="button" role="tab" onclick="window.location.href=`{{\request()->fullUrlWithQuery(['is_enable' => 1])}}`">已启用</button>
                                <button class="nav-link @if(\request()->has('is_enable') && \request()->get('is_enable') == 0) active @endif" id="nav-plugin-enable-tab" data-bs-toggle="tab" data-bs-target="#nav-plugin" type="button" role="tab" onclick="window.location.href=`{{\request()->fullUrlWithQuery(['is_enable' => 0])}}`">已禁用</button>
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
                                                @if($plugin['is_enable'] == false)
                                                <button type="button" data-unikey="{{ $plugin['unikey'] }}" data-action="activate" class="table-row btn-sm btn btn-link text-success">启用</button>
                                                @elseif($plugin['unikey'] !== 'MarketManager')

                                                @if($plugin['is_enable'] && $plugin['access_url'])
                                                <button type="button" data-unikey="{{ $plugin['unikey'] }}" data-action="setting" data-settings-url="{{ $plugin['access_url'] }}" class="table-row btn-sm btn btn-light">管理</button>
                                                @endif
                                                @if($plugin['is_enable'] && $plugin['settings_url'])
                                                <button type="button" data-unikey="{{ $plugin['unikey'] }}" data-action="setting" data-settings-url="{{ $plugin['settings_url'] }}" class="table-row btn-sm btn btn-light">设置</button>
                                                @endif

                                                <button type="button" data-unikey="{{ $plugin['unikey'] }}" data-action="deactivate" class="table-row btn-sm btn btn-link text-danger">停用</button>
                                                @endif
                                                @if($plugin['is_enable'] == false)
                                                <button type="button" data-unikey="{{ $plugin['unikey'] }}" data-action="remove" class="table-row btn-sm btn btn-link text-danger">卸载</button>
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


<div class="modal fade" id="installModal" tabindex="-2">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('plugin.install') }}" method="post">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="installModalLabel">安装插件</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3 dropdown">
                        <label class="input-group-text">安装方式</label>

                        <button class="btn btn-outline-secondary dropdown-toggle" id="toggleInstallMentod" type="button" data-bs-toggle="dropdown" aria-expanded="false">输入标识名</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="#" data-install-method="inputUnikey" placeholder="请输入插件 unikey">输入标识名</a></li>
                            <li><a class="dropdown-item" href="#" data-install-method="inputPackage" placeholder="请输入插件 unikey">输入 composer 包名</a></li>
                            <li><a class="dropdown-item" href="#" data-install-method="inputDirectory" placeholder="请输入插件所在目录">输入安装目录</a></li>
                            <li><a class="dropdown-item" href="#" data-install-method="inputZipball" placeholder="请选择插件安装包">上传 zip 压缩包</a></li>
                        </ul>

                        <input type="hidden" name="installType" value="plugin" required class="form-control" style="display: block;">
                        <input type="hidden" name="installMethod" value="inputUnikey" required class="form-control" style="display: block;">

                        <input type="text" name="inputUnikey" class="form-control" placeholder="插件 unikey" style="display: block;">
                        <input type="text" name="inputPackage" class="form-control" placeholder="扩展包 vendor/package" style="display: none;">
                        <input type="text" name="inputDirectory" class="form-control" placeholder="插件 unikey 或插件目录的绝对路径" style="display: none;">
                        <input type="file" name="inputZipball" class="form-control" style="display: none;" accept=".zip">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary install-btn">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="output" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">安装结果</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="$('#installModal').modal('show')" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="spinner-border text-primary" id="outputLoading" role="status">
                    <span class="visually-hidden">正在安装中...</span>
                </div>
                <pre></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="$('#installModal').modal('show')">取消</button>
                <button type="button" class="btn btn-primary" onclick="window.location.reload()">确认</button>
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
        $('input[name="installMethod"]').val(userChoiceInputMethod);

        const ele = dropdownMenu.parent().find(`input[name="${userChoiceInputMethod}"]`);

        if (ele) {
            $(ele).css('display', 'block').siblings('input').css('display', 'none');
            $(ele).attr('required', true).siblings('input').attr('required', false);
        }
    }));

    // 确认安装
    $(document).on('click', '.install-btn', $.debounce(250, function() {
        $(this).submit();
        $('#outputLoading').show();
        $('#output pre').empty('');
        $('#output .modal-title').text('安装结果');
        $('#installModal').modal('hide');
        $('#output').modal('show');
    }));

    $('#installModal').on('shown.bs.modal', function() {
        $('input[name="inputUnikey"]').val('');
        $('input[name="inputPackage"]').val('');
        $('input[name="inputDirectory"]').val('');
        $('input[name="inputZipball"]').val('');
    });

    // 安装
    $('form').submit(function(event) {
        event.preventDefault();

        // 减少上传文件
        if ($('input[name="installMethod"]') !== 'inputZipball') {
            $('input[name="inputZipball"]').val('');
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
                $('#output pre').html(html || '安装成功');
            },
            error: function(response) {
                $('#outputLoading').hide();
                $('#output pre').html(response.responseJSON.err_msg + "<br><br> 安装失败");
            },
        });
    });

    // 表格操作
    $(document).on('click', 'table button.table-row', $.debounce(500, function(event) {
        event.preventDefault();

        $(this).prepend('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ');
        $(this).prop('disabled', true);

        const action = $(this).data('action');
        const unikey = $(this).data('unikey');

        let data = {};
        data.plugin = unikey;
        switch (action) {
            case 'activate':
                data.is_enable = 1;
                updatePlugin(data, this);
                break;
            case 'deactivate':
                data.is_enable = 0;
                updatePlugin(data, this);
                break;
            case 'setting':
                const obj = this;
                $('#pluginPageIframe').attr('src', $(obj).data('settings-url')).on('load', function () {
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
                $('#output pre').html(res || '卸载成果');
            },
            error: function(err) {
                console.log(err)
                $('#outputLoading').hide();
                $('#output pre').html(err.responseJSON.err_msg + "<br><br> 卸载失败");
            }
        })
    }
</script>
@endsection