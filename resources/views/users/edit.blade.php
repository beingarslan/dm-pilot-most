@extends('layouts.app')

@section('title', __('Update user'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Update user')</h1>
</div>

<div class="row">
    <div class="col-md-9">

        <form role="form" method="post" action="{{ route('settings.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="form-label">@lang('Name')</label>
                                <input type="text" name="name" value="{{ $user->name }}" class="form-control" placeholder="@lang('Name')">
                            </div>

                            <div class="form-group">
                                <label class="form-label">@lang('E-mail')</label>
                                <input type="email" name="email" value="{{ $user->email }}" class="form-control" placeholder="@lang('E-mail')">
                            </div>

                            <div class="form-group">
                                <div class="form-label">@lang('Administrator')</div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="is_admin" value="1" class="custom-switch-input" {{ $user->is_admin ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">@lang('Allow access to settings')</span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="form-label">@lang('Package')</label>
                                <select name="package_id" class="form-control">
                                    <option value=""></option>
                                    @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ $package->id == $user->package_id ? 'selected' : '' }}>{{ $package->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">@lang('Package ends at')</label>
                                <input type="text" name="package_ends_at" value="{{ $user->package_ends_at }}" class="form-control dm-date-time-picker" placeholder="@lang('Package ends at')">
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="form-label">@lang('Password')</label>
                                <input type="password" name="password" value="" class="form-control" placeholder="@lang('Password')">
                            </div>

                            <div class="form-group">
                                <label class="form-label">@lang('Confirm password')</label>
                                <input type="password" name="password_confirmation" value="" class="form-control" placeholder="@lang('Password')">
                            </div>

                            <div class="form-group">
                                <label class="form-label">@lang('Trial ends at')</label>
                                <input type="text" name="trial_ends_at" value="{{ $user->trial_ends_at }}" class="form-control dm-date-time-picker" placeholder="@lang('Trial ends at')">
                            </div>

                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">@lang('Cancel')</a>
                        <button class="btn btn-blue ml-auto">@lang('Update user')</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop