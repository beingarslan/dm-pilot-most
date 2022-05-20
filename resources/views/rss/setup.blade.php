@extends('layouts.app')

@section('title', __('RSS Autopost'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">@lang('RSS Autopost')</h1>
    </div>

    @includeWhen($accounts->count() == 0, 'partials.no-accounts')

    <div class="row">
        @if($accounts->count())

            @foreach($accounts as $account)
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">

                <div class="card" data-account-info="{{ $account->username }}">
                    @if($account->has_rss)
                        <div class="card-status bg-green"></div>
                    @endif
                    <div class="d-flex align-items-center p-3">
                        <div class="avatar avatar-md mr-3" data-avatar></div>
                        <div>
                            <div>{{ $account->username }}</div>
                            <small class="d-block text-muted">@lang('Added: :time', ['time' => $account->created_at->format('M j, Y')])</small>
                        </div>
                    </div>
                    <div class="card-footer p-3">
                        <a href="{{ route('rss.index', $account) }}" class="btn btn-block btn-primary">@lang('Setup RSS')</a>
                    </div>
                </div>

            </div>
            @endforeach

        @endif

    </div>


@stop