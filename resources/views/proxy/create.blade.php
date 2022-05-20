@extends('layouts.app')

@section('title', __('Create new proxy'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Create new proxy')</h1>
</div>

<div class="row">
    <div class="col-md-9">

        <form role="form" method="post" action="{{ route('settings.proxy.store') }}">
            @csrf
            <div class="card">
                <div class="card-body">

                    <div class="form-group">
                        <label class="form-label">@lang('Server')</label>
                        <input type="text" name="server" value="{{ old('server') }}" class="form-control" placeholder="@lang('Server')">
                        <small class="help-block">@lang('Format: http://login:password@host:port or http://host:port')</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">@lang('Country')</label>
                        <select name="country" class="form-control">
                            <option value="">- @lang('Unknown') -</option>
                            @foreach($countries as $code => $name)
                                <option value="{{ $code }}" {{ old('country') == $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">@lang('Expires at')</label>
                        <input type="text" name="expires_at" value="{{ old('expires_at') }}" class="form-control dm-date-time-picker" placeholder="@lang('Expires at')">
                    </div>

                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <a href="{{ route('settings.proxy.index') }}" class="btn btn-secondary">@lang('Cancel')</a>
                        <button class="btn btn-success ml-auto">@lang('Add proxy')</button>
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