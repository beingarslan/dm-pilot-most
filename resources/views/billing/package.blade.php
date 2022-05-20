@extends('layouts.app')

@section('title', $package->title)

@section('content')

    <div class="row">
        <div class="col-md-6 col-lg-4 order-2 order-md-1">

            <div class="card">
                <div class="card-status bg-green"></div>
                <div class="card-body text-center">
                    <div class="card-category">{{ $package->title }}</div>
                    <div class="display-3 my-4">{{ $currency_symbol }}{{ $package->wholeprice }}.<sup>{{ $package->fraction_price }}</sup></div>
                    <p><span class="tag tag-rounded tag-purple">{{ __(':num days FREE trial', ['num' => config('pilot.TRIAL_DAYS')]) }}</span></p>
                    <ul class="list-unstyled leading-loose">
                        <li><strong>{{ __('pilot.package_accounts', ['num' => $package->accounts_limit]) }}</strong></li>
                        <li><strong>{{ __('pilot.storage_limit', ['mb' => $package->storage_limit]) }}</strong></li>
                        <li><strong>{{ __('pilot.messages_limit', ['num' => number_format($package->messages_limit)]) }}</strong></li>

                        @foreach($package->permissions as $permission)
                            @if($permission['can'])
                                <li><i class="fe fe-check text-success mr-2"></i> @lang($permission['description'])</li>
                            @else
                                <li><i class="fe fe-x text-danger mr-2"></i> @lang($permission['description'])</li>
                            @endif
                        @endforeach
                    </ul>
                    <small class="text-muted">
                        @lang('Prices shown in:') {{ $currency_code }}<br>
                        @lang('pilot.interval_' . $package->interval)
                    </small>
                </div>
            </div>

            <a href="{{ route('billing.index') }}" class="btn btn-secondary btn-block">
                <i class="fe fe-arrow-left"></i> @lang('Choose a different plan')
            </a>

        </div>
        <div class="col-md-6 col-lg-8 order-1 order-md-2">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('Choose a payment method')</h3>
                </div>
                <div class="card-body">

                    @if(config('services.stripe.key') && config('services.stripe.secret'))
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center">
                            <img src="{{ asset('public/img/stripe.svg') }}" height="60" alt="Stripe">
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('gateway.purchase', [$package, 'stripe']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-green btn-block">
                                    @lang('Pay using Stripe')
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr>
                    @endif

                    @if(config('services.paypal.client_id') && config('services.paypal.secret'))
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center">
                            <img src="{{ asset('public/img/paypal.svg') }}" height="60" alt="PayPal">
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('gateway.purchase', [$package, 'paypal']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block">
                                    @lang('Pay using PayPal')
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr>
                    @endif

                    @if(config('services.yandex.shop_id') && config('services.yandex.secret_key'))
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center">
                            <img src="{{ asset('public/img/yandex.svg') }}" height="60" alt="Яндекс.Касса">
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('gateway.purchase', [$package, 'yandex']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-gray-dark btn-block">
                                    @lang('Pay using Yandex.Kassa')
                                </button>
                            </form>
                        </div>
                    </div>
                    <hr>
                    @endif

                    @if(config('services.instamojo.api_key') && config('services.instamojo.auth_token'))
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center">
                            <img src="{{ asset('public/img/instamojo.svg') }}" height="60" alt="Instamojo">
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('gateway.purchase', [$package, 'instamojo']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-purple btn-block">
                                    @lang('Pay using Instamojo')
                                </button>
                            </form>
                        </div>
                    </div>
                    <hr>
                    @endif

                    @if(config('services.tinkoff.terminal_key') && config('services.tinkoff.secret_key'))
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center">
                            <img src="{{ asset('public/img/tinkoff.svg') }}" height="60" alt="Tinkoff">
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('gateway.purchase', [$package, 'tinkoff']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-yellow btn-block text-dark">
                                    @lang('Pay using Tinkoff')
                                </button>
                            </form>
                        </div>
                    </div>
                    <hr>
                    @endif

                    @if(config('services.paystack.secret'))
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center">
                            <img src="{{ asset('public/img/paystack.svg') }}" height="60" alt="Paystack">
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('gateway.purchase', [$package, 'paystack']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block">
                                    @lang('Pay using Paystack')
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

@endsection