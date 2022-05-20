@extends('layouts.app')

@section('title', __('Messages log'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            @lang('Messages log')
        </h1>
    </div>

    <div class="row">
        <div class="col-md-4 col-lg-5">
            <form method="post" action="{{ route('log.clear') }}" onsubmit="return confirm('@lang('Are you sure?')');">
                @csrf
                <input type="hidden" name="account" value="{{ Request::get('account') }}">
                <div class="form-group">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fe fe-x"></i> @lang('Clear messages on queue')
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-3 col-lg-3">
            <form method="get" action="{{ route('log.view') }}" autocomplete="off">
            <div class="form-group">
                <select name="account" class="form-control">
                    <option value="">@lang('Account')</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ (Request::get('account') == $account->id ? 'selected' : '') }}>{{ $account->username }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="form-group">
                <select name="status" class="form-control">
                    <option value="">@lang('Status')</option>
                    <option value="{{ config('pilot.MESSAGE_STATUS_ON_QUEUE') }}" {{ (Request::get('status') == config('pilot.MESSAGE_STATUS_ON_QUEUE') ? 'selected' : '') }}>@lang('Messages on queue')</option>
                    <option value="{{ config('pilot.MESSAGE_STATUS_SUCCESS') }}" {{ (Request::get('status') == config('pilot.MESSAGE_STATUS_SUCCESS') ? 'selected' : '') }}>@lang('Sent messages')</option>
                    <option value="{{ config('pilot.MESSAGE_STATUS_FAILED') }}" {{ (Request::get('status') == config('pilot.MESSAGE_STATUS_FAILED') ? 'selected' : '') }}>@lang('Failed to sent messages')</option>
                </select>
            </div>
        </div>
        <div class="col-md-2 col-lg-1 text-right text-nowrap">
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fe fe-filter"></i>
                </button>
            </div>
            </form>
        </div>
    </div>

    @if($data->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <tbody>
                        @foreach($data as $log)
                        <tr>
                            <td>
                                {{ $log->account->username }}
                                &rarr;
                                {{ $log->recipients }}

                                <div class="small">
                                    @switch($log->status)
                                        @case(config('pilot.MESSAGE_STATUS_SUCCESS'))
                                            <span class="status-icon bg-success"></span>
                                            @break

                                        @case(config('pilot.MESSAGE_STATUS_ON_QUEUE'))
                                            <span class="status-icon bg-warning"></span>
                                            @break

                                        @case(config('pilot.MESSAGE_STATUS_FAILED'))
                                            <span class="status-icon bg-danger"></span>
                                            @break

                                        @default
                                    @endswitch
                                    <span class="text-muted">{{ $log->send_at->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td class="text-right">
                                <span class="badge badge-default">
                                    @lang('pilot.message_type_' . $log->message_type)
                                </span>
                                @isset($log->options['message'])
                                    <div>{!! wordwrap($log->options['message'], 100, '<br>') !!}</div>
                                @endisset

                                @if($log->comment)
                                    <div class="small text-muted">{{ $log->comment }}</div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{ $data->appends( Request::all() )->links() }}

    @if($data->count() == 0)
        <div class="alert alert-primary text-center">
            <i class="fe fe-alert-triangle mr-2"></i> @lang('No messages found')
        </div>
    @endif
@stop