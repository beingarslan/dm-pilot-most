@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row">
        <div class="col col-login mx-auto">
            <div class="text-center mb-6">
                <img src="{{ asset(config('pilot.LOGO.BACKEND')) }}" class="h-6" alt="">
            </div>
            <form class="card" action="{{ route('login') }}" method="post">
                @csrf
                <div class="card-body p-6">
                    <div class="card-title">@lang('Login to your account')</div>
                    <div class="form-group">
                        <label for="email" class="form-label">@lang('E-Mail Address')</label>
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="@lang('Enter email')" tabindex="1" required autofocus>
                        @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">
                            @lang('Password')
                            <a href="{{ route('password.request') }}" class="float-right small">@lang('I forgot password')</a>
                        </label>
                        <input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" placeholder="Password" tabindex="2">
                        @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" name="remember" id="remember" tabindex="3" {{ old( 'remember') ? 'checked' : '' }}>
                            <span class="custom-control-label">@lang('Remember me')</span>
                        </label>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary btn-block" tabindex="4">@lang('Sign in')</button>
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
                @lang('Don\'t have account yet?') <a href="{{ route('register') }}">@lang('Sign up')</a>

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
