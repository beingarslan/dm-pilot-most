@extends('layouts.app')

@section('title', __('Profile'))

@section('content')
<form role="form" method="post" action="{{ route('profile.update') }}" autocomplete="off">
    @csrf
    @method('PUT')

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

    <div class="row row-deck">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('Profile')</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">@lang('Name')</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="@lang('Full name')">
                    </div>
                    <div class="form-group">
                        <label class="form-label">E-mail</label>
                        <input type="email" value="{{ $user->email }}" class="form-control disabled" placeholder="E-mail" disabled>
                        <small class="help-block">@lang('E-mail can\'t be changed')</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('Change password')</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">@lang('Password')</label>
                        <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" placeholder="@lang('Password')">
                    </div>
                    <div class="form-group">
                        <label class="form-label">@lang('Confirm password')</label>
                        <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password_confirmation" placeholder="@lang('Confirm password')">
                    </div>
                    <div class="alert alert-info">
                        <i class="fe fe-info mr-2"></i> @lang('Type new password if you would like to change current password.')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-right">
        <button class="btn btn-primary ml-auto">@lang('Update profile')</button>
    </div>
</form>
@stop