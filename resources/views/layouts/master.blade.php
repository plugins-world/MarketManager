<!DOCTYPE html>
<html lang="{{ \App::getLocale() }}">
<head>
    @include('MarketManager::commons.head', [
        'title' => 'Plugin MarketManager',
    ])

    {{-- Laravel Mix - CSS File --}}
    {{-- <link rel="stylesheet" href="{{ mix('css/market-manager.css') }}"> --}}
</head>

<body>
    <div class="position-relative">
        @yield('content')

        @include('MarketManager::commons.toast')
    </div>

    @yield('bodyjs')

    {{-- Laravel Mix - JS File --}}
    {{-- <script src="{{ mix('js/market-manager.js') }}"></script> --}}
</body>
</html>
