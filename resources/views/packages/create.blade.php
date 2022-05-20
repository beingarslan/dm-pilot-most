@extends('layouts.app')

@section('title', __('Create new package'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Create new package')</h1>
</div>

<div class="row">
    <div class="col-md-9">

        <form role="form" method="post" action="{{ route('settings.packages.store') }}">
            @csrf
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Title')</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="@lang('Title')">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Price')</label>
                                <input type="number" min="0" step="0.01" name="price" value="{{ old('price') }}" class="form-control" placeholder="@lang('Price')">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Payment interval')</label>
                                <select name="interval" class="form-control">
                                    <option value="day" {{ old('interval') == 'day' ? 'selected' : '' }}>@lang('pilot.interval_day')</option>
                                    <option value="week" {{ old('interval') == 'week' ? 'selected' : '' }}>@lang('pilot.interval_week')</option>
                                    <option value="month" {{ old('interval') == 'month' ? 'selected' : '' }}>@lang('pilot.interval_month')</option>
                                    <option value="year" {{ old('interval') == 'year' ? 'selected' : '' }}>@lang('pilot.interval_year')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Number of accounts')</label>
                                <input type="number" min="1" name="settings[accounts_count]" value="{{ old('settings.accounts_count') }}" class="form-control" placeholder="@lang('Number of accounts')">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Messages limit per interval')</label>
                                <input type="number" min="1" name="settings[messages_count]" value="{{ old('settings.messages_count') }}" class="form-control" placeholder="@lang('Messages limit per interval')">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Media storage size (MB)')</label>
                                <input type="number" min="1" name="settings[storage]" value="{{ old('settings.storage') }}" class="form-control" placeholder="@lang('Media storage size (MB)')">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-label">@lang('Permissions')</div>
                                <div class="custom-controls-stacked">
                                    @foreach($permissions as $permission => $description)
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="settings[{{ $permission }}]" value="true" {{ old('settings.permission_' . $permission) ? 'checked' : '' }}>
                                        <span class="custom-control-label">@lang($description)</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">

                            <div class="form-group">
                                <div class="form-label">@lang('Featured')</div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="is_featured" value="1" class="custom-switch-input" {{ old('is_featured') ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">@lang('Highlight as most featured package')</span>
                                </label>
                            </div>

                            <div class="form-group">
                                <div class="form-label">@lang('Package visibility')</div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="is_hidden" value="1" class="custom-switch-input" {{ old('is_hidden') ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">@lang('Make this package visible to the public')</span>
                                </label>
                            </div>

                        </div>
                    </div>



                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <a href="{{ route('settings.packages.index') }}" class="btn btn-secondary">@lang('Cancel')</a>
                        <button class="btn btn-success ml-auto">@lang('Add package')</button>
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