@extends('layouts.app')

@section('title', __('Notifications'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            @lang('Notifications')
        </h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('Notifications')</h3>
            <div class="card-options">
                <form method="post" action="{{ route('mark.notifications') }}" autocomplete="off" class="d-flex">
                    @csrf
                    @method('PUT')
                    <button type="submit" name="mark" class="btn btn-sm btn-primary mr-2">
                        <i class="fe fe-check"></i> @lang('Mark all as read')
                    </button>
                    <button type="submit" name="delete" class="btn btn-sm btn-secondary">
                        <i class="fe fe-x"></i> @lang('Delete')
                    </button>
                </form>
            </div>
        </div>

        @if($data->count() > 0)
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <tbody>
                    @foreach($data as $notification)
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

    {{ $data->appends( Request::all() )->links() }}

    @if($data->count() == 0)
        <div class="alert alert-primary text-center">
            <i class="fe fe-alert-triangle mr-2"></i> @lang('No notifications found')
        </div>
    @endif
@stop