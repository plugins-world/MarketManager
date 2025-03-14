<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>{{ $title ?? '' }}</title>

@stack('headcss')

<link href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- topbar js: @see documention https://buunguyen.github.io/topbar/ -->
<script src="https://cdn.jsdelivr.net/npm/topbar"></script>
<!-- jquery: @see documention http://jquery.cuishifeng.cn/ -->
<script src="https://cdn.jsdelivr.net/npm/jquery"></script>
<!-- jquery throttle and debounce: @see https://stackoverflow.com/questions/27787768/debounce-function-in-jquery -->
<script src="https://cdn.jsdelivr.net/npm/jquery-throttle-debounce"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@iframe-resizer/parent@5.3.2"></script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>

<style>
    iframe {
        width: 100%;
        height: calc(100vh - 100px);
    }
</style>

<script>
    $(function() {
        // Ajax global setting
        $.ajaxSetup({
            xhrFields: {
                withCredentials: true,
            },
            crossDomain: true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });

        // Animate requests globally
        $(document).ajaxStart(function() {
            topbar.show();
        });

        // Animate the end of requests globally
        $(document).ajaxComplete(function() {
            topbar.hide()
        });
        // iFrame Resizer
        // @see http://davidjbradshaw.github.io/iframe-resizer/
        $('iframe').each(index => {
            $($('iframe')[index]).on('load', function (obj) {
                iframeResize({
                    license: 'GPLv3',
                    waitForLoad: true,
                    // warningTimeout: 0,
                    scrolling: true,
                }, $(this)[0]);

                $(this).find('.form-bottom-wrapper').hide();
            })
        });

        $(document).on('click', 'form button[type="submit"]', $.debounce(500, function(event) {
            event.preventDefault();

            $(this).prepend('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ');
            $(this).prop('disabled', true);
        }));

        $('#settingForm form').submit(function(event) {
            event.preventDefault();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                data: new FormData($(this)[0]),
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);

                    $('.toast').toast('show');
                    $('form button[type="submit"]').prop('disabled', false);
                    top.location.reload();
                },
                error: function(error) {
                    console.error(error);
                    $('.toast').find('.toast-body').html(error.responseJSON.message || error.responseJSON.err_msg || '未知错误');
                    $('.toast').toast('show');
                    $('form button[type="submit"] span').remove();
                    $('form button[type="submit"]').prop('disabled', false);
                },
            });
        });
    });
</script>

@stack('headjs')
