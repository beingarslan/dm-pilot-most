@extends('layouts.app')

@section('content')

    @includeWhen($accounts->count() == 0, 'partials.no-accounts')

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="card d-none d-md-block">
        <img src="{{ asset('public/img/card-header.png') }}" class="card-img-top">
    </div>

    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md bg-blue mr-3">
                        <i class="fe fe-play"></i>
                    </span>
                    <div>
                        <h4 class="m-0"><a href="{{ route('autopilot.index') }}">{{ $autopilots_count }} <small>@lang('Autopilot')</small></a></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md bg-green mr-3">
                      <i class="fe fe-shopping-cart"></i>
                    </span>
                    <div>
                        <h4 class="m-0"><a href="{{ route('account.index') }}">{{ $accounts_count }} <small>@lang('Accounts')</small></a></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md bg-red mr-3">
                        <i class="fe fe-message-square"></i>
                    </span>
                    <div>
                        <h4 class="m-0"><a href="{{ route('list.index', 'messages') }}">{{ $messages_list_count }} <small>@lang('Messages lists')</small></a></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md bg-yellow mr-3">
                      <i class="fe fe-users"></i>
                    </span>
                    <div>
                        <h4 class="m-0"><a href="{{ route('list.index', 'users') }}">{{ $users_list_count }} <small>@lang('Users lists')</small></a></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
       <div class="col-sm-12 col-md-4">
            <div class="card">
                <div class="card-body p-3 text-center">
                    <div class="text-right">
                        <a href="{{ route('log.view', ['status' => config('pilot.MESSAGE_STATUS_ON_QUEUE')]) }}" class="btn btn-sm btn-secondary">
                            <i class="fe fe-eye"></i> @lang('View all')
                        </a>
                    </div>
                    <div class="h1 m-0">{{ $messages['on_queue']['total'] }}</div>
                    <div class="text-muted mb-4">@lang('Messages on queue')</div>
                    <div class="progress progress-sm">
                       <div class="progress-bar bg-blue" style="width: {{ $messages['on_queue']['percentage'] }}%"></div>
                    </div>
                </div>
            </div>
       </div>
       <div class="col-sm-12 col-md-4">
            <div class="card">
                <div class="card-body p-3 text-center">
                    <div class="text-right">
                        <a href="{{ route('log.view', ['status' => config('pilot.MESSAGE_STATUS_SUCCESS')]) }}" class="btn btn-sm btn-secondary">
                            <i class="fe fe-eye"></i> @lang('View all')
                        </a>
                    </div>
                    <div class="h1 m-0">{{ $messages['sent']['total'] }}</div>
                    <div class="text-muted mb-4">@lang('Sent messages')</div>
                    <div class="progress progress-sm">
                       <div class="progress-bar bg-green" style="width: {{ $messages['sent']['percentage'] }}%"></div>
                    </div>
                </div>
            </div>
       </div>
       <div class="col-sm-12 col-md-4">
            <div class="card">
                <div class="card-body p-3 text-center">
                    <div class="text-right">
                        <a href="{{ route('log.view', ['status' => config('pilot.MESSAGE_STATUS_FAILED')]) }}" class="btn btn-sm btn-secondary">
                            <i class="fe fe-eye"></i> @lang('View all')
                        </a>
                    </div>
                    <div class="h1 m-0">{{ $messages['failed']['total'] }}</div>
                    <div class="text-muted mb-4">@lang('Failed to sent messages')</div>
                    <div class="progress progress-sm">
                       <div class="progress-bar bg-red" style="width: {{ $messages['failed']['percentage'] }}%"></div>
                    </div>
                </div>
            </div>
       </div>
    </div>

    @if($all_accounts->count() > 0)
    <div class="chart-container">
        <div class="d-flex flex-row justify-content-between align-items-center">
            <div class="mb-3">
                <h3 class="card-title">@lang('Monthly statistics')</h3>
            </div>
            <div class="mb-3">
                <select name="account_id" class="form-control w-auto">
                    @foreach($all_accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->username }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="card chart-widget" data-type="{{ config('pilot.STATISTICS_MEDIA') }}" data-title="@lang('Media posts')" data-color="#5eba00">
                    <div class="card-body">
                        <div class="card-value float-right rise"></div>
                        <h3 class="mb-1 total-count"></h3>
                        <div class="text-muted widget-title"></div>
                    </div>
                    <div class="card-chart-bg">
                        <div class="chart-ui" style="height: 100%"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="card chart-widget" data-type="{{ config('pilot.STATISTICS_FOLLOWERS') }}" data-title="@lang('Followers')" data-color="#f1c40f">
                    <div class="card-body">
                        <div class="card-value float-right rise"></div>
                        <h3 class="mb-1 total-count"></h3>
                        <div class="text-muted widget-title"></div>
                    </div>
                    <div class="card-chart-bg">
                        <div class="chart-ui" style="height: 100%"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="card chart-widget" data-type="{{ config('pilot.STATISTICS_FOLLOWING') }}" data-title="@lang('Following')" data-color="#467fcf">
                    <div class="card-body">
                        <div class="card-value float-right rise"></div>
                        <h3 class="mb-1 total-count"></h3>
                        <div class="text-muted widget-title"></div>
                    </div>
                    <div class="card-chart-bg">
                        <div class="chart-ui" style="height: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('Notifications')</h3>
                    <div class="card-options">
                        <a href="{{ route('notifications') }}" class="btn btn-sm btn-primary">@lang('View all')</a>
                    </div>
                </div>

                @if($notifications->count() > 0)
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <tbody>
                            @foreach($notifications as $notification)
                            <tr>
                                <td>
                                    {!! __('pilot.notification_' . $notification->data['action'], $notification->data) !!}
                                    <div class="small text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="text-right">
                                    @if($notification->read_at == null)
                                    <span class="badge badge-success">@lang('NEW')</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('Accounts')</h3>
                    <div class="card-options">
                        <a href="{{ route('account.index') }}" class="btn btn-sm btn-primary">@lang('View all')</a>
                    </div>
                </div>

                @if($accounts->count() > 0)
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap" id="accounts">
                        <tbody>
                            @foreach($accounts as $account)
                            <tr data-account-info="{{ $account->username }}">
                                <td class="text-center" width="68">
                                    <div class="avatar d-block" data-avatar></div>
                                </td>
                                <td>
                                    <a href="{{ route('account.edit', $account) }}">{{ $account->username }}</a>
                                    <div class="small text-muted">
                                        @lang('Added: :time', ['time' => $account->created_at->format('M j, Y')])
                                    </div>
                                </td>
                                <td align="right">
                                    <div class="tag" title="@lang('Last sync:') {{ optional($account->followers_sync_at)->diffForHumans() ?? __('Not synchronized') }}">
                                        <span data-followers>{{ $account->followers_count }}</span>
                                        <span class="tag-addon tag-green"><i class="fe fe-users"></i></span>
                                    </div>
                                    <div class="tag ml-1" title="@lang('Last sync:') {{ optional($account->following_sync_at)->diffForHumans() ?? __('Not synchronized') }}">
                                        <span data-following>{{ $account->following_count }}</span>
                                        <span class="tag-addon tag-blue"><i class="fe fe-user-plus"></i></span>
                                    </div>
                                    <div class="tag ml-1">
                                        <span data-posts>{{ $account->posts_count }}</span>
                                        <span class="tag-addon tag-red"><i class="fe fe-image"></i></span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

@endsection