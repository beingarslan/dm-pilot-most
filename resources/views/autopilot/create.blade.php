@extends('layouts.app')

@section('title', __('Add autopilot'))

@section('content')

    @includeWhen($accounts->count() == 0, 'partials.no-accounts')

    @if($accounts->count())
        <form role="form" method="post" action="{{ route('autopilot.store') }}" autocomplete="off">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('Setup new autopilot')</h3>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label class="form-label">@lang('Send message to')</label>

                        <div class="selectgroup w-100">
                            <label class="selectgroup-item">
                                <input type="radio" name="action" value="1" class="selectgroup-input" {{ old('action', 1) == '1' ? 'checked' : '' }}>
                                <span class="selectgroup-button">@lang('New Followers')</span>
                            </label>

                            <label class="selectgroup-item">
                                <input type="radio" name="action" value="2" class="selectgroup-input" {{ old('action') == '2' ? 'checked' : '' }}>
                                <span class="selectgroup-button">@lang('Unfollowers')</span>
                            </label>

                            <label class="selectgroup-item">
                                <input type="radio" name="action" value="3" class="selectgroup-input" {{ old('action') == '3' ? 'checked' : '' }}>
                                <span class="selectgroup-button">@lang('New Following')</span>
                            </label>

                            <label class="selectgroup-item">
                                <input type="radio" name="action" value="4" class="selectgroup-input" {{ old('action') == '4' ? 'checked' : '' }}>
                                <span class="selectgroup-button">@lang('Unfollowing')</span>
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Account')</label>
                                <select name="account_id" class="form-control">
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account') == $account->id ? 'selected' : '' }}>{{ $account->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Name')</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="@lang('Autopilot name')">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-8">
                            <div class="form-group">
                                <div class="form-label">@lang('Activity period')</div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="activity_period" class="custom-switch-input">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">@lang('I want to set specific date &amp; time')</span>
                                </label>
                            </div>
                            <div class="row" id="date_and_time" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Starts at')</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fe fe-calendar"></i></span>
                                            <input type="text" name="starts_at" value="{{ old('starts_at') }}" class="form-control {{ $errors->has('starts_at') ? 'is-invalid' : '' }} dm-date-time-picker" placeholder="@lang('Starts at')">
                                        </div>
                                        <small class="help-block">@lang('Autopilot start time and date (optional)')</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Ends at')</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fe fe-calendar"></i></span>
                                            <input type="text" name="ends_at" value="{{ old('ends_at') }}" class="form-control {{ $errors->has('ends_at') ? 'is-invalid' : '' }} dm-date-time-picker" placeholder="@lang('Ends at')">
                                        </div>
                                        <small class="help-block">@lang('Autopilot stop time and date (optional)')</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-label">@lang('Message')</div>
                        <div class="custom-controls-stacked">
                            <label class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" name="message_type" value="list" {{ old('message_type', 'list') == 'list' ? 'checked' : '' }}>
                                <div class="custom-control-label">@lang('Predefined list of messages')</div>
                            </label>
                            <label class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" name="message_type" value="text" {{ old('message_type') == 'text' ? 'checked' : '' }}>
                                <div class="custom-control-label">@lang('Custom text')</div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="option_list">
                        <label class="form-label">@lang('Messages list')</label>
                        <select name="lists_id" class="form-control {{ $errors->has('lists_id') ? 'is-invalid' : '' }}">
                            <option value=""></option>
                            @foreach($lists as $list)
                                <option value="{{ $list->id }}" {{ old('lists_id') == $list->id ? 'selected' : '' }}>{{ $list->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="option_message" style="display: none;">
                        <label class="form-label">@lang('Text')</label>
                        <textarea rows="3" name="text" class="form-control {{ $errors->has('text') ? 'is-invalid' : '' }}" placeholder="@lang('Compose a message to be sent')" data-emojiable="true">{{ old('text') }}</textarea>
                        <small class="help-block">@lang('We also support Spintax. Feel free to use it like: {Hi|Hello|Hey} dear friend! {Thank you|We appreciate you} for your interest.')</small>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <a href="{{ route('autopilot.index') }}" class="btn btn-secondary">@lang('Cancel')</a>
                        <button class="btn btn-success ml-auto">@lang('Add autopilot')</button>
                    </div>
                </div>
            </form>
        </div>
    @endif
@stop

@push('scripts')
    <script type="text/javascript">
    $(function() {

        @if(old('message_type'))
            $('input[name="message_type"][value="{{ old('message_type') }}"]').trigger('change');
        @endif

        @if(old('activity_period'))
            $('input[name="activity_period"]').trigger('click');
        @endif

    });
    </script>
@endpush