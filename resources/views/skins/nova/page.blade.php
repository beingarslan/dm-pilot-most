<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @includeWhen(config('pilot.GOOGLE_ANALYTICS'), 'partials.google-analytics')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ config('pilot.SITE_DESCRIPTION') }}">
    <meta name="keywords" content="{{ config('pilot.SITE_KEYWORDS') }}">
    <title>{{ __(config('app.name')) }} &mdash; {{ __(config('pilot.SITE_DESCRIPTION')) }}</title>
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,400i,600,700,700i&amp;subset=latin-ext" rel="stylesheet">
    <link href="{{ asset('public/skins/nova/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('public/skins/nova/css/fontawesome-all.css') }}" rel="stylesheet">
    <link href="{{ asset('public/skins/nova/css/swiper.css') }}" rel="stylesheet">
    <link href="{{ asset('public/skins/nova/css/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset('public/skins/nova/css/styles.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset(config('pilot.LOGO.FAVICON')) }}">
</head>
<body data-spy="scroll" data-target=".fixed-top">

    <!-- Preloader -->
    <div class="spinner-wrapper">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
    <!-- end of preloader -->

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <!-- Text Logo - Use this if you don't have a graphic logo -->
        <!-- <a class="navbar-brand logo-text" href="{{ url('/') }}">{{ __(config('app.name')) }}</a> -->

        <!-- Image Logo -->
        <a class="navbar-brand logo-image" href="{{ url('/') }}"><img src="{{ asset(config('pilot.LOGO.FRONTEND')) }}" alt="alternative"></a>

        <!-- Mobile Menu Toggle Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-awesome fas fa-bars"></span>
            <span class="navbar-toggler-awesome fas fa-times"></span>
        </button>
        <!-- end of mobile menu toggle button -->

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="{{ route('landing') }}#header">@lang('Home')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="{{ route('landing') }}#features">@lang('Features')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="{{ route('landing') }}#pricing">@lang('Pricing')</a>
                </li>
                @if(count($pages))
                    @foreach($pages as $p)
                        <li class="nav-item"><a class="nav-link page-scroll" href="{{ route('page', $p) }}">{{ $p->title }}</a></li>
                    @endforeach
                @endif
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $user->name }}</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('dashboard') }}"><span class="item-text">@lang('Dashboard')</span></a>
                            <div class="dropdown-items-divide-hr"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"><span class="item-text">@lang('Logout')</span></a>
                        </div>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" aria-haspopup="true" aria-expanded="false">@lang('Login')</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('login') }}"><span class="item-text">@lang('Login')</span></a>
                            <div class="dropdown-items-divide-hr"></div>
                            <a class="dropdown-item" href="{{ route('register') }}"><span class="item-text">@lang('Register')</span></a>
                        </div>
                    </li>
                @endauth

                <!-- Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $active_language['native'] }}</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        @foreach($languages as $code => $language)
                            <a href="{{ route('localize', $code) }}" rel="alternate" hreflang="{{ $code }}" class="dropdown-item"><span class="item-text">{{ $language['native'] }}</span></a>
                        @endforeach
                    </div>
                </li>
                <!-- end of dropdown menu -->

            </ul>
        </div>
    </nav> <!-- end of navbar -->
    <!-- end of navbar -->


    <!-- Header -->
    <header id="header" class="header">
        <div class="header-content" style="min-height: 600px;">
            <div class="container">
                <div class="text-container">
                    <h1><span class="turquoise">{{ $page->title }}</span></h1>
                    <p class="p-large">{!! $page->description !!}</p>
                </div> <!-- end of text-container -->
            </div> <!-- end of container -->
        </div> <!-- end of header-content -->
    </header> <!-- end of header -->
    <!-- end of header -->

    <!-- Details 1 -->
    <div class="basic-1">
        <div class="container">
            <div class="text-container">
                <h2></h2>

            </div> <!-- end of text-container -->
        </div> <!-- end of container -->
    </div> <!-- end of basic-1 -->
    <!-- end of details 1 -->

    <!-- Copyright -->
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p class="p-small">Copyright &copy;{{ date('Y') }} &bull; {{ __(config('app.name')) }} &mdash; {{ __(config('pilot.SITE_DESCRIPTION')) }}</p>
                </div> <!-- end of col -->
            </div> <!-- enf of row -->
        </div> <!-- end of container -->
    </div> <!-- end of copyright -->
    <!-- end of copyright -->


    <!-- Scripts -->
    <script src="{{ asset('public/skins/nova/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/skins/nova/js/popper.min.js') }}"></script>
    <script src="{{ asset('public/skins/nova/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/skins/nova/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('public/skins/nova/js/swiper.min.js') }}"></script>
    <script src="{{ asset('public/skins/nova/js/jquery.magnific-popup.js') }}"></script>
    <script src="{{ asset('public/skins/nova/js/scripts.js') }}"></script>
</body>
</html>