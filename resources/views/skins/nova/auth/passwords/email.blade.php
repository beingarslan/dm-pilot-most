@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row">
        <div class="col col-login mx-auto">
            <div class="text-center mb-6">
                <img src="{{ asset(config('pilot.LOGO.BACKEND')) }}" class="h-6" alt="">
            </div>

            <form class="card" action="{{ route('password.email') }}" method="post">
                @csrf
                <div class="card-body p-6">

                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="card-title">@lang('Forgot password')</div>

                    <p class="text-muted">@lang('Enter your email address and your password will be reset and emailed to you.')</p>

                    <div class="form-group">
                        <label for="email" class="form-label">@lang('E-Mail Address')</label>
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="@lang('E-Mail Address')" required>
                        @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary btn-block">@lang('Send me password reset Link')</button>
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