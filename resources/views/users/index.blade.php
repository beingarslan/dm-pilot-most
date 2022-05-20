@extends('layouts.app')

@section('title', __('Users'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Users')</h1>
    <div class="page-options">
        <a href="{{ route('settings.users.create') }}" class="btn btn-success">
            <i class="fe fe-plus"></i> @lang('Create new user')
        </a>
    </div>
</div>

<form method="get" action="{{ route('settings.users.index') }}" autocomplete="off">
    <div class="row no-gutters">
        <div class="col-5 col-md-5 col-lg-4">
            <div class="form-group">
                <select name="filter" class="form-control">
                    <option value="">@lang('All users')</option>
                    <option value="has_accounts" {{ (Request::get('filter') == 'has_accounts' ? 'selected' : '') }}>@lang('Has accounts')</option>
                    <option value="has_bots" {{ (Request::get('filter') == 'has_bots' ? 'selected' : '') }}>@lang('Has chat bots')</option>
                    <option value="has_rss" {{ (Request::get('filter') == 'has_rss' ? 'selected' : '') }}>@lang('Has RSS')</option>
                    <option value="has_media" {{ (Request::get('filter') == 'has_media' ? 'selected' : '') }}>@lang('Has media')</option>
                    <option value="no_accounts" {{ (Request::get('filter') == 'no_accounts' ? 'selected' : '') }}>@lang('No accounts')</option>
                    <option value="on_trial" {{ (Request::get('filter') == 'on_trial' ? 'selected' : '') }}>@lang('On trial')</option>
                    <option value="active" {{ (Request::get('filter') == 'active' ? 'selected' : '') }}>@lang('Active subscription')</option>
                    <option value="expired" {{ (Request::get('filter') == 'expired' ? 'selected' : '') }}>@lang('Expired subscription')</option>
                </select>
            </div>
        </div>
        <div class="col-5 col-md-5 col-lg-4">
            <div class="form-group ml-3 mr-3">
                <input type="text" name="search" value="{{ Request::get('search') }}" class="form-control" placeholder="@lang('Search')">
            </div>
        </div>
        <div class="col-2 col-md-2 col-lg-4">
            <button class="btn btn-primary" type="submit">
                <i class="fe fe-filter"></i>
            </button>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-md-9">

        @if($data->count() > 0)
            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Subscription')</th>
                                <th>@lang('Accounts')</th>
                                <th>@lang('Chat Bots')</th>
                                <th>@lang('RSS')</th>
                                <th>@lang('Storage')</th>
                                <th class="text-right">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('settings.users.edit', $item) }}">{{ $item->name }}</a>
                                    <small class="d-block text-muted">{{ $item->email }}</small>
                                </td>
                                <td>
                                    @if($item->subscribed())
                                        <span class="text-green"><i class="fe fe-check"></i> @lang('Active')</span>
                                        <small class="d-block text-muted">
                                            @lang('Expires in:') {{ $item->package_ends_at->diffForHumans() }}
                                        </small>
                                    @else
                                        <span class="text-muted"><i class="fe fe-x"></i> @lang('Not active')</span>
                                        @if($item->package_ends_at)
                                        <small class="d-block text-muted">
                                            @lang('Expired:') {{ $item->package_ends_at->diffForHumans() }}
                                        </small>
                                        @endif
                                    @endif

                                    @if($item->onTrial())
                                        <span class="text-yellow">@lang('(On trial)')</span>
                                        <small class="d-block text-muted">
                                            @lang('Expires in') {{ $item->trial_ends_at->diffForHumans() }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="tag">{{ $item->accounts_count }}</span>
                                </td>
                                <td>
                                    <span class="tag">{{ $item->bots_count }}</span>
                                </td>
                                <td>
                                    <span class="tag">{{ $item->rss_count }}</span>
                                </td>
                                <td>
                                    <small class="text-gray">{{ $item->used_space }} / {{ $item->storage_limit }}</small>
                                </td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-secondary btn-clean" data-toggle="dropdown">
                                            <i class="fe fe-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('settings.users.edit', $item) }}" class="dropdown-item">@lang('Edit user')</a>

                                            <a href="{{ route('settings.users.accounts', $item) }}" class="dropdown-item">@lang('View accounts')</a>

                                            <a href="{{ route('settings.users.login_as', $item) }}" class="dropdown-item">@lang('Login as')</a>

                                            <form method="post" action="{{ route('settings.users.destroy', $item) }}" onsubmit="return confirm('@lang('Confirm delete?')');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item">
                                                    @lang('Delete')
                                                </button>
                                            </form>
                                        </div>
                                    </div>
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
                <i class="fe fe-alert-triangle mr-2"></i> @lang('No users found')
            </div>
        @endif

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop