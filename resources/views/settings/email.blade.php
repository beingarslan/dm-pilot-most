@extends('layouts.app')

@section('title', __('E-mail Settings'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('E-mail Settings')</h1>
</div>

<div class="row">
    <div class="col-md-9">

        <form role="form" method="post" action="{{ route('settings.update', 'email') }}" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('SMTP Host')</label>
                                <input type="text" name="settings[MAIL_HOST]" value="{{ config('mail.mailers.smtp.host') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('SMTP Port')</label>
                                <input type="text" name="settings[MAIL_PORT]" value="{{ config('mail.mailers.smtp.port') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">@lang('SMTP Username')</label>
                                <input type="text" name="settings[MAIL_USERNAME]" value="{{ config('mail.mailers.smtp.username') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">@lang('SMTP Password')</label>
                                <input type="text" name="settings[MAIL_PASSWORD]" value="{{ config('mail.mailers.smtp.password') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">@lang('SMTP Encryption')</label>
                                <select name="settings[MAIL_ENCRYPTION]" class="form-control">
                                    <option value="" {{ null == config('mail.mailers.smtp.encryption') ? 'selected' : '' }}>@lang('No encryption')</option>
                                    <option value="tls" {{ 'tls' == config('mail.mailers.smtp.encryption') ? 'selected' : '' }}>@lang('TLS')</option>
                                    <option value="ssl" {{ 'ssl' == config('mail.mailers.smtp.encryption') ? 'selected' : '' }}>@lang('SSL')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('From address')</label>
                                <input type="text" name="settings[MAIL_FROM_ADDRESS]" value="{{ config('mail.from.address') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('From name')</label>
                                <input type="text" name="settings[MAIL_FROM_NAME]" value="{{ config('mail.from.name') }}" class="form-control">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" name="action" value="test" class="btn btn-secondary btn-block">
                                <i class="fe fe-mail mr-2"></i> @lang('Test SMTP settings')
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" name="action" value="save" class="btn btn-primary btn-block">
                                <i class="fe fe-save mr-2"></i> @lang('Save settings')
                            </button>
                        </div>
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