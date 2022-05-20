@extends('layouts.app')

@section('title', __('System upgrade'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('System upgrade')</h1>
</div>

<div class="row">
    <div class="col-md-9">

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="alert alert-primary text-center m-0" role="alert">
                            @lang('You are currently using DM Pilot version :version', ['version' => config('pilot.version')])
                        </div>

                    </div>
                    <div class="col-md-4">
                        <form role="form" method="post" action="{{ route('settings.upgrade.check') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fe fe-info mr-2"></i> @lang('Check for upgrade')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @if (session('is_upgradable'))
                <div class="card-footer">
                    <form role="form" method="post" action="{{ route('settings.upgrade.upgrade') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="new_version" value="{{ session('new_version') }}">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fe fe-check mr-2"></i> @lang('Upgrade to the latest version :version', ['version' => session('new_version')])
                        </button>
                    </form>
                </div>
            @endif
        </div>

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop