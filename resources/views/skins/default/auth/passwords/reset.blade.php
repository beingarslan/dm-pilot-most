@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row">
        <div class="col col-login mx-auto">
            <div class="text-center mb-6">
                <img src="{{ asset(config('pilot.LOGO.BACKEND')) }}" class="h-6" alt="">
            </div>

            <form class="card" action="{{ route('password.update') }}" method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="card-body p-6">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="card-title">@lang('Reset Password')</div>
                    <div class="form-group">
                        <label for="email" class="form-label">@lang('E-Mail Address')</label>
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}"  placeholder="@lang('E-Mail Address')" required>
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
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary btn-block">@lang('Reset Password')</button>
                    </div>
                </div>
            </form>
            <div class="text-center text-muted">
                @lang('Forget it, <a href=":link">send me back</a> to the sign in screen.', ['link' => route('login')])
            </div>
        </div>
    </div>
</div>
@endsection
