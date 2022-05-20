<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        @includeWhen(config('pilot.GOOGLE_ANALYTICS'), 'partials.google-analytics')

        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset(config('pilot.LOGO.FAVICON')) }}" type="image/png">
        <title>{{ __(config('app.name')) }} &mdash; {{ __(config('pilot.SITE_DESCRIPTION')) }}</title>
        <meta name="description" content="{{ config('pilot.SITE_DESCRIPTION') }}">
        <meta name="keywords" content="{{ config('pilot.SITE_KEYWORDS') }}">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('public/skins/default/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('public/skins/default/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/skins/default/vendors/owl-carousel/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/skins/default/vendors/animate-css/animate.css') }}">
        <!-- main css -->
        <link rel="stylesheet" href="{{ asset('public/skins/default/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('public/skins/default/css/responsive.css') }}">
    </head>
    <body data-spy="scroll" data-target="#mainNav" data-offset="70">

        <!--================Header Menu Area =================-->
        <header class="header_area">
            <div class="main_menu" id="mainNav">
            	<nav class="navbar navbar-expand-lg navbar-light">
					<div class="container">
						<!-- Brand and toggle get grouped for better mobile display -->
						<a class="navbar-brand logo_h" href="{{ url('/') }}"><img src="{{ asset(config('pilot.LOGO.FRONTEND')) }}" alt=""></a>
						<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<!-- Collect the nav links, forms, and other content for toggling -->
						<div class="collapse navbar-collapse offset" id="navbarSupportedContent">
							<ul class="nav navbar-nav menu_nav ml-auto">
								<li class="nav-item active"><a class="nav-link" href="#home">@lang('Home')</a></li>
								<li class="nav-item"><a class="nav-link" href="#feature">@lang('Features')</a></li>
								<li class="nav-item"><a class="nav-link" href="#price">@lang('Pricing')</a></li>
                                @if(count($pages))
                                    @foreach($pages as $p)
                                        <li class="nav-item"><a class="nav-link" href="{{ route('page', $p) }}">{{ $p->title }}</a></li>
                                    @endforeach
                                @endif
								@auth
								    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}"><strong>{{ $user->name }}</strong></a></li>
                                @else
                                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">@lang('Login')</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">@lang('Register')</a></li>
                                @endauth
							</ul>
						</div>
					</div>
            	</nav>
            </div>
        </header>
        <!--================Header Menu Area =================-->

        <!--================Home Banner Area =================-->
        <section class="home_banner_area" id="home">
            <div class="banner_inner">
				<div class="container">
					<div class="row banner_content">
						<div class="col-lg-8">
							<h2>@lang('Instagram Most Wanted Automation Tool for Direct Message & Scheduled Posts.')</h2>
							<p>@lang('Greet with warm welcome message your new followers by sending customized text message. Create your own list of users and send them text message with emoji, post, hashtag, video or photo.')</p>
							<a class="banner_btn" href="{{ route('billing.index') }}">@lang('Try now')</a>
						</div>
						<div class="col-lg-4">
							<div class="banner_map_img">
								<img class="img-fluid" src="{{ asset('public/skins/default/img/dm-landing.png') }}" alt="">
							</div>
						</div>
					</div>
				</div>
            </div>
        </section>
        <!--================End Home Banner Area =================-->

        <!--================Feature Area =================-->
        <section class="feature_area p_120" id="feature">
        	<div class="container">

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fe fe-check mr-2"></i> {!! session('success') !!}
                    </div>
                @endif

        		<div class="main_title">
        			<h2>@lang('Unique Features')</h2>
        			<p>@lang('Features you will definently love.')</p>
        		</div>
        		<div class="feature_inner row">
        			<div class="col-lg-3 col-md-6">
        				<div class="feature_item text-center">
        					<img src="{{ asset('public/skins/default/img/icon/f-icon-1.png') }}" alt="">
        					<h4>@lang('Free Trial')</h4>
        					<p>@lang('Try out our service for free. No credit card required.')</p>
        				</div>
        			</div>
        			<div class="col-lg-3 col-md-6">
        				<div class="feature_item text-center">
        					<img src="{{ asset('public/skins/default/img/icon/f-icon-1.png') }}" alt="">
        					<h4>@lang('Multiple Accounts')</h4>
        					<p>@lang('Add and manage multiple accounts at the same time')</p>
        				</div>
        			</div>
        			<div class="col-lg-3 col-md-6">
        				<div class="feature_item text-center">
        					<img src="{{ asset('public/skins/default/img/icon/f-icon-1.png') }}" alt="">
        					<h4>@lang('Scheduled posts')</h4>
        					<p>@lang('Schedule your posts and enjoy your free time')</p>
        				</div>
        			</div>
        			<div class="col-lg-3 col-md-6">
        				<div class="feature_item text-center">
        					<img src="{{ asset('public/skins/default/img/icon/f-icon-1.png') }}" alt="">
        					<h4>@lang('Photo, Video and Album')</h4>
        					<p>@lang('Publish any content on your feed or stories')</p>
        				</div>
        			</div>
                    <div class="col-lg-3 col-md-6">
                        <div class="feature_item text-center">
                            <img src="{{ asset('public/skins/default/img/icon/f-icon-1.png') }}" alt="">
                            <h4>@lang('Welcome new followers')</h4>
                            <p>@lang('Greet with warm welcome message your new followers by sending customized text message.')</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="feature_item text-center">
                            <img src="{{ asset('public/skins/default/img/icon/f-icon-1.png') }}" alt="">
                            <h4>@lang('Keep unfollowers')</h4>
                            <p>@lang('Automatically send promocode or any other catchy message to keep your followers and don\'t let them to unfollow you.')</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="feature_item text-center">
                            <img src="{{ asset('public/skins/default/img/icon/f-icon-1.png') }}" alt="">
                            <h4>@lang('Message any content to any size of users list')</h4>
                            <p>@lang('Create your own list of users and send them text message with emoji, post, hashtag, video or photo even disappearing.')</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="feature_item text-center">
                            <img src="{{ asset('public/skins/default/img/icon/f-icon-1.png') }}" alt="">
                            <h4>@lang('Web-Based Direct Messenger')</h4>
                            <p>@lang('Chat without touching your device and chat with your customers directly from the browser.')</p>
                        </div>
                    </div>
        		</div>
        	</div>
        </section>
        <!--================End Feature Area =================-->

        <!--================Price Area =================-->
        <section class="price_area p_120" id="price">
        	<div class="container">
        		<div class="main_title">
        			<h2>@lang('Pricing table')</h2>
        			<p>@lang('Affordable prices will surprise you!')</p>
        		</div>
        		<div class="price_item_inner row">
                    @foreach($packages as $package)
        			<div class="col-md-6 col-lg-{{ 12 / count($packages) }}">
        				<div class="price_item">
        					<div class="price_head">
        						<div class="text-center">
        							<h3>{{ $package->title }}</h3>
        						</div>
        						<div class="text-center">
        							<h2 class="mt-4 mb-4">{{ $currency_symbol }}{{ $package->wholeprice }}.<sup>{{ $package->fraction_price }}</sup></h2>
                                    <h3><span class="badge badge-pill badge-primary">{{ __(':num days FREE trial', ['num' => config('pilot.TRIAL_DAYS')]) }}</span></h3>
        						</div>
        					</div>
        					<div class="price_body">
        						<ul class="list">
                                    <li><strong>{{ __('pilot.package_accounts', ['num' => $package->accounts_limit]) }}</strong></li>
                                    <li><strong>{{ __('pilot.storage_limit', ['mb' => $package->storage_limit]) }}</strong></li>
                                    <li><strong>{{ __('pilot.messages_limit', ['num' => number_format($package->messages_limit)]) }}</strong></li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Post types: Photo, Video, Album')</li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Scheduled Posts and Stories')</li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Web Based Direct Messenger')</li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Web Based Direct Messenger')</li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Send Bulk Messages')</li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Custom users lists')</li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Scheduled Autopilot')</li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Pre-defined messages lists')</li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Detect Unfollowers')</li>
                                    <li><i class="fa fa-check text-success mr-1"></i> @lang('Spintax Support')</li>
        						</ul>
                                <br>
                                <small class="text-muted">
                                    @lang('Prices shown in:') {{ $currency_code }}<br>
                                    @lang('pilot.interval_' . $package->interval)
                                </small>
        					</div>
        					<div class="price_footer">
                                <a class="main_btn2" href="{{ route('billing.index') }}">@lang('Try now')</a>
        					</div>
        				</div>
        			</div>
                    @endforeach
        		</div>
        	</div>
        </section>
        <!--================End Price Area =================-->

        <!--================ start footer Area  =================-->
        <footer class="footer-area p_30">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div class="footer-text">
                            Copyright &copy;{{ date('Y') }} &bull; {{ __(config('app.name')) }} &mdash; {{ __(config('pilot.SITE_DESCRIPTION')) }}
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group dropup">
                            <button type="button" class="btn btn-sm btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ $active_language['native'] }}
                            </button>
                            <div class="dropdown-menu">
                                @foreach($languages as $code => $language)
                                    <a href="{{ route('localize', $code) }}" rel="alternate" hreflang="{{ $code }}" class="dropdown-item">{{ $language['native'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
		<!--================ End footer Area  =================-->

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="{{ asset('public/skins/default/js/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ asset('public/skins/default/js/popper.js') }}"></script>
        <script src="{{ asset('public/skins/default/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('public/skins/default/js/stellar.js') }}"></script>
        <script src="{{ asset('public/skins/default/js/theme.js') }}"></script>
    </body>
</html>