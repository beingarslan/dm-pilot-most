@extends('layouts.app')

@section('title', __('Billing'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            @lang('Billing')
        </h1>
    </div>

    @if($subscribed)
        <div class="alert text-center alert-success">
            <i class="fe fe-check mr-2"></i> @lang('Your subscription for <strong>:package</strong> package is currently active and expires in <strong>:expires_in</strong> days!', ['package' => $subscription_title, 'expires_in' => $subscription_expires_in])
        </div>
    @endif

    @if($on_trial)
        <div class="alert text-center alert-warning">
            <i class="fe fe-alert-triangle mr-2"></i>@lang('Your are on a <strong>Trial</strong> period which expires in :expires_in days.', ['expires_in' => $subscription_expires_in])
        </div>
    @endif

    @if(!$on_trial && !$subscribed)
        <div class="alert text-center alert-warning">
            <i class="fe fe-alert-triangle mr-2"></i>@lang('Your subscription has been ended. Please choose a plan and pay.')
        </div>
    @endif

    <div class="row">
        @foreach($packages as $package)
            <div class="col-sm-6 col-lg-{{ 12 / count($packages) }}">

                <div class="card">
                    @if ($package->is_featured)
                        <div class="card-status bg-green"></div>
                    @endif
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
                        <div class="text-center mt-6">
                            @if ($package->is_featured == 1)
                                <a href="{{ route('billing.package', $package) }}" class="btn btn-green btn-block">
                                    <i class="fe fe-check mr-2"></i> @lang('Choose plan')
                                </a>
                            @else
                                <a href="{{ route('billing.package', $package) }}" class="btn btn-secondary btn-block">
                                    <i class="fe fe-check mr-2"></i> @lang('Choose plan')
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    @if($subscribed)
        <div class="text-right">
            <form action="{{ route('billing.cancel') }}" method="POST" onsubmit="return confirm('@lang('Confirm cancel subscription?')');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-secondary btn-clean">
                    <i class="fe fe-x-circle"></i> @lang('Cancel subscription') &ndash; {{ $subscription_title }}
                </button>
            </form>
        </div>
    @endif

@endsection