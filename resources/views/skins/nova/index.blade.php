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
                    <a class="nav-link page-scroll" href="#header">@lang('Home')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="#features">@lang('Features')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="#pricing">@lang('Pricing')</a>
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
        <div class="header-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="text-container">
                            <h1><span class="turquoise">@lang('Instagram Most Wanted Automation Tool for')</span> @lang('Direct Message & Scheduled Posts.')</h1>
                            <p class="p-large">@lang('Greet with warm welcome message your new followers by sending customized text message. Create your own list of users and send them text message with emoji, post, hashtag, video or photo.')</p>
                            <a class="btn-solid-lg" href="{{ route('billing.index') }}">@lang('Try now')</a>
                        </div> <!-- end of text-container -->
                    </div> <!-- end of col -->
                    <div class="col-lg-6">
                        <div class="image-container">
                            <img class="img-fluid" src="{{ asset('public/skins/nova/images/header-teamwork.svg') }}" alt="alternative">
                        </div> <!-- end of image-container -->
                    </div> <!-- end of col -->
                </div> <!-- end of row -->
            </div> <!-- end of container -->
        </div> <!-- end of header-content -->
    </header> <!-- end of header -->
    <!-- end of header -->


    <!-- Customers -->
    <div class="slider-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h5>@lang('Trusted By')</h5>

                    <!-- Image Slider -->
                    <div class="slider-container">
                        <div class="swiper-container image-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="image-container">
                                        <img class="img-responsive" src="{{ asset('public/skins/nova/images/customer-logo-1.png') }}" alt="alternative">
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="image-container">
                                        <img class="img-responsive" src="{{ asset('public/skins/nova/images/customer-logo-2.png') }}" alt="alternative">
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="image-container">
                                        <img class="img-responsive" src="{{ asset('public/skins/nova/images/customer-logo-3.png') }}" alt="alternative">
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="image-container">
                                        <img class="img-responsive" src="{{ asset('public/skins/nova/images/customer-logo-4.png') }}" alt="alternative">
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="image-container">
                                        <img class="img-responsive" src="{{ asset('public/skins/nova/images/customer-logo-5.png') }}" alt="alternative">
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="image-container">
                                        <img class="img-responsive" src="{{ asset('public/skins/nova/images/customer-logo-6.png') }}" alt="alternative">
                                    </div>
                                </div>
                            </div> <!-- end of swiper-wrapper -->
                        </div> <!-- end of swiper container -->
                    </div> <!-- end of slider-container -->
                    <!-- end of image slider -->

                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of slider-1 -->
    <!-- end of customers -->


    <!-- Services -->
    <div id="features" class="cards-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>@lang('Unique Features')</h2>
                    <p class="p-heading p-large">@lang('Features you will definently love.')</p>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
            <div class="row">
                <div class="col-lg-12">

                    <!-- Card -->
                    <div class="card card-equal-h">
                        <img class="card-image" src="{{ asset('public/skins/nova/images/services-icon-1.svg') }}" alt="alternative">
                        <div class="card-body">
                            <h4 class="card-title">@lang('Welcome new followers')</h4>
                            <p>@lang('Greet with warm welcome message your new followers by sending customized text message.')</p>
                        </div>
                    </div>
                    <!-- end of card -->

                    <!-- Card -->
                    <div class="card card-equal-h">
                        <img class="card-image" src="{{ asset('public/skins/nova/images/services-icon-2.svg') }}" alt="alternative">
                        <div class="card-body">
                            <h4 class="card-title">@lang('Web-Based Direct Messenger')</h4>
                            <p>@lang('Chat without touching your device and chat with your customers directly from the browser.')</p>
                        </div>
                    </div>
                    <!-- end of card -->

                    <!-- Card -->
                    <div class="card card-equal-h">
                        <img class="card-image" src="{{ asset('public/skins/nova/images/services-icon-3.svg') }}" alt="alternative">
                        <div class="card-body">
                            <h4 class="card-title">@lang('Scheduled posts and stories')</h4>
                            <p>@lang('Publish or schedule any content on your feed or stories.')</p>
                        </div>
                    </div>
                    <!-- end of card -->

                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of cards-1 -->
    <!-- end of services -->


    <!-- Details 1 -->
    <div class="basic-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="text-container">
                        <h2>@lang('Design And Plan Your Business Growth Steps')</h2>
                        <p>@lang('Use DM Pilot to design and plan your business growth strategy.')</p>
                        <a class="btn-solid-reg" href="{{ route('billing.index') }}">@lang('Try now')</a>
                    </div> <!-- end of text-container -->
                </div> <!-- end of col -->
                <div class="col-lg-6">
                    <div class="image-container">
                        <img class="img-fluid" src="{{ asset('public/skins/nova/images/details-1-office-worker.svg') }}" alt="alternative">
                    </div> <!-- end of image-container -->
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of basic-1 -->
    <!-- end of details 1 -->


    <!-- Details 2 -->
    <div class="basic-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="image-container">
                        <img class="img-fluid" src="{{ asset('public/skins/nova/images/details-2-office-team-work.svg') }}" alt="alternative">
                    </div> <!-- end of image-container -->
                </div> <!-- end of col -->
                <div class="col-lg-6">
                    <div class="text-container">
                        <h2>@lang('Add and manage multiple accounts at the same time')</h2>
                        <ul class="list-unstyled li-space-lg">
                            <li class="media">
                                <i class="fas fa-check"></i>
                                <div class="media-body">@lang('Basically we will teach you step by step what you need to do')</div>
                            </li>
                            <li class="media">
                                <i class="fas fa-check"></i>
                                <div class="media-body">@lang('In order to develop your company and reach new heights')</div>
                            </li>
                            <li class="media">
                                <i class="fas fa-check"></i>
                                <div class="media-body">@lang('Everyone will be pleased from stakeholders to employees')</div>
                            </li>
                        </ul>
                        <a class="btn-solid-reg" href="{{ route('billing.index') }}">@lang('Try now')</a>
                    </div> <!-- end of text-container -->
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of basic-2 -->
    <!-- end of details 2 -->

    <!-- Pricing -->
    <div id="pricing" class="cards-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>@lang('Pricing table')</h2>
                    <p>@lang('Affordable prices will surprise you!')</p>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
            <div class="row">
                <div class="col-lg-12">

                    @foreach($packages as $package)
                    <!-- Card-->
                    <div class="card">
                        @if($package->is_featured)
                        <div class="label">
                            <p class="best-value">@lang('Best Value')</p>
                        </div>
                        @endif
                        <div class="card-body">
                            <div class="card-title">{{ $package->title }}</div>
                            <div class="card-subtitle">{{ __(':num days FREE trial', ['num' => config('pilot.TRIAL_DAYS')]) }}</div>
                            <hr class="cell-divide-hr">
                            <div class="price">
                                <span class="currency">{{ $currency_symbol }}</span><span class="value">{{ $package->wholeprice }}.<sup>{{ $package->fraction_price }}</sup></span>
                                <div class="frequency">@lang('pilot.interval_' . $package->interval)</div>
                            </div>
                            <hr class="cell-divide-hr">
                            <ul class="list-unstyled li-space-lg">
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">{{ __('pilot.package_accounts', ['num' => $package->accounts_limit]) }}</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">{{ __('pilot.storage_limit', ['mb' => $package->storage_limit]) }}</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">{{ __('pilot.messages_limit', ['num' => number_format($package->messages_limit)]) }}</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">@lang('Post types: Photo, Video, Album')</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">@lang('Scheduled Posts and Stories')</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">@lang('Web Based Direct Messenger')</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">@lang('Send Bulk Messages')</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">@lang('Custom users lists')</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">@lang('Scheduled Autopilot')</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">@lang('Pre-defined messages lists')</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">@lang('Detect Unfollowers')</div>
                                </li>
                                <li class="media">
                                    <i class="fas fa-check"></i><div class="media-body">@lang('Spintax Support')</div>
                                </li>
                            </ul>
                            <div class="button-wrapper">
                                <a class="btn-solid-reg" href="{{ route('billing.index') }}">@lang('Try now')</a>
                            </div>
                        </div>
                    </div> <!-- end of card -->
                    <!-- end of card -->
                    @endforeach

                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of cards-2 -->
    <!-- end of pricing -->

    <!-- Video -->
    <div class="basic-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>@lang('Check Out The Video')</h2>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
            <div class="row">
                <div class="col-lg-12">

                    <!-- Video Preview -->
                    <div class="image-container">
                        <div class="video-wrapper">
                            <a class="popup-youtube" href="https://www.youtube.com/watch?v=Bey4XXJAqS8" data-effect="fadeIn">
                                <img class="img-fluid" src="{{ asset('public/skins/nova/images/video-frame.svg') }}" alt="alternative">
                                <span class="video-play-button">
                                    <span></span>
                                </span>
                            </a>
                        </div> <!-- end of video-wrapper -->
                    </div> <!-- end of image-container -->
                    <!-- end of video preview -->

                    <p>@lang('This video will show you a case study for one of our <strong>Major Customers</strong> and will help you understand why your startup needs DM Pilot in this highly competitive market')</p>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of basic-3 -->
    <!-- end of video -->


    <!-- Testimonials -->
    <div class="slider-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="image-container">
                        <img class="img-fluid" src="{{ asset('public/skins/nova/images/testimonials-2-men-talking.svg') }}" alt="alternative">
                    </div> <!-- end of image-container -->
                </div> <!-- end of col -->
                <div class="col-lg-6">
                    <h2>@lang('Testimonials')</h2>

                    <!-- Card Slider -->
                    <div class="slider-container">
                        <div class="swiper-container card-slider">
                            <div class="swiper-wrapper">

                                <!-- Slide -->
                                <div class="swiper-slide">
                                    <div class="card">
                                        <img class="card-image" src="{{ asset('public/skins/nova/images/testimonial-1.svg') }}" alt="alternative">
                                        <div class="card-body">
                                            <p class="testimonial-text">@lang('I just finished my trial period and was so amazed with the support and results that I purchased DM Pilot right away at the special price.')</p>
                                            <p class="testimonial-author">@lang('Jude Thorn - SMM Manager')</p>
                                        </div>
                                    </div>
                                </div> <!-- end of swiper-slide -->
                                <!-- end of slide -->

                                <!-- Slide -->
                                <div class="swiper-slide">
                                    <div class="card">
                                        <img class="card-image" src="{{ asset('public/skins/nova/images/testimonial-2.svg') }}" alt="alternative">
                                        <div class="card-body">
                                            <p class="testimonial-text">@lang('DM Pilot has always helped or startup to position itself in the highly competitive market of mobile applications. You will not regret using it!')</p>
                                            <p class="testimonial-author">@lang('Marsha Singer - Developer')</p>
                                        </div>
                                    </div>
                                </div> <!-- end of swiper-slide -->
                                <!-- end of slide -->

                                <!-- Slide -->
                                <div class="swiper-slide">
                                    <div class="card">
                                        <img class="card-image" src="{{ asset('public/skins/nova/images/testimonial-3.svg') }}" alt="alternative">
                                        <div class="card-body">
                                            <p class="testimonial-text">@lang('Love their services and was so amazed with the support and results that I purchased DM Pilot for two years in a row. They are awesome.')</p>
                                            <p class="testimonial-author">@lang('Roy Smith - Marketer')</p>
                                        </div>
                                    </div>
                                </div> <!-- end of swiper-slide -->
                                <!-- end of slide -->

                            </div> <!-- end of swiper-wrapper -->

                            <!-- Add Arrows -->
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <!-- end of add arrows -->

                        </div> <!-- end of swiper-container -->
                    </div> <!-- end of slider-container -->
                    <!-- end of card slider -->

                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of slider-2 -->
    <!-- end of testimonials -->


    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="footer-col">
                        <h4>@lang('About DM Pilot')</h4>
                        <p>@lang('This tools allows you to automate your daily routine and enjoy your SMM strategy.')</p>
                    </div>
                </div> <!-- end of col -->
                <div class="col-md-4">
                    <div class="footer-col last">
                        <h4>@lang('Social Media')</h4>
                        <span class="fa-stack">
                            <a href="#your-link">
                                <i class="fas fa-circle fa-stack-2x"></i>
                                <i class="fab fa-facebook-f fa-stack-1x"></i>
                            </a>
                        </span>
                        <span class="fa-stack">
                            <a href="#your-link">
                                <i class="fas fa-circle fa-stack-2x"></i>
                                <i class="fab fa-twitter fa-stack-1x"></i>
                            </a>
                        </span>
                        <span class="fa-stack">
                            <a href="#your-link">
                                <i class="fas fa-circle fa-stack-2x"></i>
                                <i class="fab fa-google-plus-g fa-stack-1x"></i>
                            </a>
                        </span>
                        <span class="fa-stack">
                            <a href="#your-link">
                                <i class="fas fa-circle fa-stack-2x"></i>
                                <i class="fab fa-instagram fa-stack-1x"></i>
                            </a>
                        </span>
                        <span class="fa-stack">
                            <a href="#your-link">
                                <i class="fas fa-circle fa-stack-2x"></i>
                                <i class="fab fa-linkedin-in fa-stack-1x"></i>
                            </a>
                        </span>
                    </div>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of footer -->
    <!-- end of footer -->


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