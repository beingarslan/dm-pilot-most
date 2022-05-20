@extends('layouts.auth')

@if(config('recaptcha.api_site_key') && config('recaptcha.api_secret_key'))
    @push('head')
        {!! htmlScriptTagJsApi() !!}
    @endpush
@endif

@section('content')
<div class="container">
    <div class="row">
        <div class="col col-login mx-auto">
            <div class="text-center mb-6">
                <img src="{{ asset(config('pilot.LOGO.BACKEND')) }}" class="h-6" alt="">
            </div>

            <form class="card" action="{{ route('register') }}" method="post">
                @csrf

                <div class="card-body p-6">
                    <div class="card-title">@lang('Create new account')</div>
                    <div class="form-group">
                        <label for="name" class="form-label">@lang('Name')</label>
                        <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" placeholder="@lang('Enter name')" required autofocus>
                        @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">@lang('Email address')</label>
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="@lang('Enter email')" required>
                        @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">@lang('Password')</label>
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="@lang('Password')" required>
                        @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">@lang('Confirm password')</label>
                        <input id="password_confirmation" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password_confirmation" placeholder="@lang('Confirm password')" required>
                    </div>
                    @if(config('recaptcha.api_site_key') && config('recaptcha.api_secret_key'))
                    <div class="form-group">
                        {!! htmlFormSnippet() !!}
                        @if ($errors->has('g-recaptcha-response'))
                            <div class="text-red mt-1">
                                <small><strong>{{ $errors->first('g-recaptcha-response') }}</strong></small>
                            </div>
                        @endif
                    </div>
                    @endif
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary btn-block">@lang('Create new account')</button>
                    </div>
                    <div class="form-footer">
                        @if(config('services.facebook.client_id') && config('services.facebook.client_secret'))
                            <a href="{{ route('login.social', 'facebook') }}" class="btn btn-block btn-facebook"><i class="fe fe-facebook"></i> @lang('Login with Facebook')</a>
                        @endif
                        @if(config('services.google.client_id') && config('services.google.client_secret'))
                            <a href="{{ route('login.social', 'google') }}" class="btn btn-block btn-google"><i class="fe fe-log-in"></i> @lang('Login with Google')</a>
                        @endif
                    </div>
                </div>
            </form>
            <div class="text-center text-muted">
                @lang('Already have account?') <a href="{{ route('login') }}">@lang('Sign in')</a>

                <div class="mt-5">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
    </div>
</div>
@endsection