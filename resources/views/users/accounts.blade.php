@extends('layouts.app')

@section('title', __('User\'s accounts'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang(':name\'s accounts', ['name' => $user->name])</h1>
</div>

<div class="row">
    <div class="col-md-9">

        @if($accounts->count() > 0)
            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap" id="accounts">
                        <thead>
                            <tr>
                                <th class="w-1"></th>
                                <th>@lang('Account')</th>
                                <th>@lang('Statistic')</th>
                                <th>@lang('Messages')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                            <tr data-account-info="{{ $account->username }}">
                                <td class="text-center" width="68">
                                    <div class="avatar d-block" data-avatar></div>
                                </td>
                                <td>
                                    {{ $account->username }}
                                    <div class="small text-muted">
                                        @lang('Added: :time', ['time' => $account->created_at->format('M j, Y')])
                                    </div>
                                </td>
                                <td>
                                    <div class="tag" title="@lang('Last sync:') {{ optional($account->followers_sync_at)->diffForHumans() ?? __('Not synchronized') }}">
                                        <span data-followers>{{ $account->followers_count }}</span>
                                        <span class="tag-addon tag-green">@lang('Followers')</span>
                                    </div>
                                    <div class="tag ml-1" title="@lang('Last sync:') {{ optional($account->following_sync_at)->diffForHumans() ?? __('Not synchronized') }}">
                                        <span data-following>{{ $account->following_count }}</span>
                                        <span class="tag-addon tag-blue">@lang('Following')</span>
                                    </div>
                                    <div class="tag ml-1">
                                        <span data-posts>{{ $account->posts_count }}</span>
                                        <span class="tag-addon tag-red">@lang('Posts')</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="tag">
                                        <span>{{ $account->messages_on_queue_count }}</span> <span class="tag-addon tag-blue">@lang('On queue')</span>
                                    </div>
                                    <div class="tag ml-1">
                                        <span>{{ $account->messages_sent_count }}</span> <span class="tag-addon tag-green">@lang('Sent')</span>
                                    </div>
                                    <div class="tag ml-1">
                                        <span>{{ $account->messages_failed_count }}</span> <span class="tag-addon tag-red">@lang('Failed')</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-primary text-center">
                <i class="fe fe-alert-triangle mr-2"></i> @lang('No accounts found')
            </div>
        @endif

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop