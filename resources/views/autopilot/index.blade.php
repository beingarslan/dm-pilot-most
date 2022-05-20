@extends('layouts.app')

@section('title', __('Autopilot'))

@section('content')
    <form method="get" action="{{ route('autopilot.index') }}" autocomplete="off">
        <div class="row">
            <div class="col-md-2">
                <h1 class="page-title">
                    @lang('Autopilot')
                </h1>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <select name="account" class="form-control">
                        <option value="">@lang('All accounts')</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ (Request::get('account') == $account->id ? 'selected' : '') }}>{{ $account->username }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 text-right">
                <div class="form-group">
                    <select name="action" class="form-control">
                        <option value="">@lang('Activity')</option>
                        <option value="1" {{ (Request::get('action') == '1' ? 'selected' : '') }}>@lang('New Followers')</option>
                        <option value="2" {{ (Request::get('action') == '2' ? 'selected' : '') }}>@lang('Unfollowers')</option>
                        <option value="3" {{ (Request::get('action') == '3' ? 'selected' : '') }}>@lang('New Following')</option>
                        <option value="4" {{ (Request::get('action') == '4' ? 'selected' : '') }}>@lang('Unfollowing')</option>
                    </select>
                </div>
            </div>
            <div class="col-9 col-md-3 text-right">
                <div class="form-group">
                    <input type="text" name="search" value="{{ Request::get('search') }}" class="form-control" placeholder="@lang('Search')">
                </div>
            </div>
            <div class="col-3 col-md-1 text-right">
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">
                        <i class="fe fe-filter"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('Autopilot')</h3>
            <div class="card-options">
                <a href="{{ route('autopilot.create') }}" class="btn btn-success">
                    <i class="fe fe-plus"></i> @lang('Add autopilot')
                </a>
            </div>
        </div>

        @if($data->count() > 0)
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>@lang('Name')</th>
                        <th>@lang('Account')</th>
                        <th>@lang('Period')</th>
                        <th>@lang('Activity')</th>
                        <th class="text-right">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $autopilot)
                    <tr>
                        <td>
                            <a href="{{ route('autopilot.edit', $autopilot) }}">{{ $autopilot->name }}</a>
                            <div class="small text-muted">
                                @lang('Added: :time', ['time' => $autopilot->created_at->format('M j, Y')])
                            </div>
                        </td>
                        <td>
                            <span class="tag tag-blue">{{ $autopilot->account->username }}</span>
                        </td>
                        <td>
                            @if($autopilot->starts_at || $autopilot->ends_at)
                                <span class="tag">{{ optional($autopilot->starts_at)->format('H:i, d M Y') ?? __('Now') }}</span>
                                &ndash;
                                <span class="tag">{{ optional($autopilot->ends_at)->format('H:i, d M Y') ?? __('Forever') }}</span>
                            @else
                                <span class="tag">@lang('Forever')</span>
                            @endif
                        </td>
                        <td>{{ __('pilot.autopilot_action_' . $autopilot->action) }}</td>
                        <td class="text-right">
                            <form method="post" action="{{ route('autopilot.destroy', $autopilot) }}" onsubmit="return confirm('@lang('Confirm delete?')');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary btn-clean">
                                    <i class="fe fe-trash"></i> @lang('Delete')
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{ $data->appends( Request::all() )->links() }}

    @if($data->count() == 0)
        <div class="alert alert-primary text-center">
            <i class="fe fe-alert-triangle mr-2"></i> @lang('No autopilot found')
        </div>
    @endif

@stop