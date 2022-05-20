@extends('layouts.app')

@section('title', __('Accounts'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">@lang('Accounts')</h1>
        <div class="page-options">
            <a href="{{ route('account.create') }}" class="btn btn-success">
                <i class="fe fe-plus"></i> @lang('Add account')
            </a>
        </div>
    </div>

    @if($data->count() > 0)
        <div class="row">
            @foreach($data as $account)
            <div class="col-sm-6 col-md-6 col-lg-4">
                <div class="card" data-account-info="{{ $account->username }}">
                    <div class="card-body p-0 text-center">
                        <div class="avatar avatar-xxl mt-4" data-avatar></div>
                        <div class="h4 mt-4 mb-1">
                            @if($account->is_active)
                                <span class="status-icon bg-success"></span>
                            @else
                                <span class="status-icon bg-secondary"></span>
                            @endif
                            <a href="https://www.instagram.com/{{ $account->username }}/" target="_blank">{{ $account->username }}</a>
                        </div>
                        <div class="small text-muted mb-4">
                            @lang('Added: :time', ['time' => $account->created_at->format('M j, Y')])
                        </div>

                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3 pl-2">
                            <div>
                                @lang('Followers')
                                <div class="small text-muted">@lang('Last sync:')</div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-default badge-pill p-1 pl-2 pr-2" data-followers>
                                    {{ $account->followers_count }}
                                </span>
                                <div class="small text-muted">
                                    @if($account->followers_sync_at)
                                        <a href="{{ route('account.export', [$account, 'followers']) }}" title="@lang('Export list')" class="text-decoration-none"><i class="fe fe-download"></i></a>
                                        {{ $account->followers_sync_at->diffForHumans() }}
                                    @else
                                        @lang('Not synchronized')
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3 pl-2">
                            <div>
                                @lang('Following')
                                <div class="small text-muted">@lang('Last sync:')</div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-default badge-pill p-1 pl-2 pr-2" data-following>
                                    {{ $account->following_count }}
                                </span>
                                <div class="small text-muted">
                                    @if($account->following_sync_at)
                                        <a href="{{ route('account.export', [$account, 'following']) }}" title="@lang('Export list')" class="text-decoration-none"><i class="fe fe-download"></i></a>
                                        {{ $account->following_sync_at->diffForHumans() }}
                                    @else
                                        @lang('Not synchronized')
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3 pl-2">
                            @lang('Posts')
                            <span class="badge badge-default badge-pill p-1 pl-2 pr-2" data-posts>
                                {{ $account->posts_count }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3 pl-2">
                            @lang('Messages on queue')
                            <span class="badge badge-primary badge-pill p-1 pl-2 pr-2">
                                {{ $account->messages_on_queue_count }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3 pl-2">
                            @lang('Sent messages')
                            <span class="badge badge-success badge-pill p-1 pl-2 pr-2">
                                {{ $account->messages_sent_count }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3 pl-2">
                            @lang('Failed to sent messages')
                            <span class="badge badge-danger badge-pill p-1 pl-2 pr-2">
                                {{ $account->messages_failed_count }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3 pl-2">
                            <div>
                                <a href="{{ route('account.edit', $account) }}" class="btn btn-outline-primary btn-sm">@lang('Edit account')</a>
                            </div>
                            <div>
                                @if($account->is_active == false)
                                    <a href="{{ route('account.confirm', $account) }}" class="btn btn-outline-success btn-sm">@lang('Confirm account')</a>
                                @endif
                            </div>
                            <div>
                                <form method="post" action="{{ route('account.destroy', $account) }}" onsubmit="return confirm('@lang('Confirm delete?')');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        @lang('Delete')
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            @endforeach
        </div>

        {{ $data->appends( Request::all() )->links() }}
    @else
        <div class="alert alert-primary text-center">
            <i class="fe fe-alert-triangle mr-2"></i> @lang('No accounts found')
        </div>
    @endif

@stop