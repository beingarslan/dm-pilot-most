@extends('layouts.app')

@section('title', __('License validation'))

@section('content')
<form role="form" method="post" action="{{ route('settings.license.verify') }}" autocomplete="off">
    @csrf

    <div class="row justify-content-md-center">
        <div class="col-md-10 col-lg-6">

            <div class="alert text-center alert-warning">
                <i class="fe fe-alert-triangle mr-2"></i>@lang('Please verify your DM Pilot license.')
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('Enter your Purchase Code')</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">@lang('Purchase Code')</label>
                        <input type="text" name="code" value="{{ old('code') }}" class="form-control" placeholder="********-****-****-****-************" maxlength="36" required autofocus>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary btn-block">@lang('Validate license')</button>
                </div>
            </div>
            <small class="text-muted">
                <strong>@lang('How to find your purchase code:')</strong>
                <ol>
                    <li>@lang('Log into your Envato Market account.')</li>
                    <li>@lang('Hover the mouse over your username at the top of the screen.')</li>
                    <li>@lang('Click &laquo;Downloads&raquo; from the drop-down menu.')</li>
                    <li>@lang('Click &laquo;License certificate &amp; purchase code&raquo; (available as PDF or text file).')</li>
                </ol>
            </small>
        </div>
    </div>

</form>
@stop