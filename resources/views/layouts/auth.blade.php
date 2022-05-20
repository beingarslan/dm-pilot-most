<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}" />
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#4188c9">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <link rel="icon" href="{{ asset('public/favicon.ico') }}" type="image/x-icon" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('public/favicon.ico') }}" />
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('public/css/dm.bundle.css') }}?v={{ config('pilot.version') }}">
    @stack('head')
    @includeWhen(config('pilot.GOOGLE_ANALYTICS'), 'partials.google-analytics')
</head>

<body>
    <div class="page">
        <div class="page-single">
            @yield('content')
        </div>
    </div>
    <script src="{{ asset('public/js/dm.bundle.js') }}?v={{ config('pilot.version') }}" type="text/javascript"></script>
    @stack('scripts')
</body>

</html>