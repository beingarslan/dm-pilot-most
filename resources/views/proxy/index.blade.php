@extends('layouts.app')

@section('title', __('Proxies'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Proxies')</h1>
    <div class="page-options">
        <a href="{{ route('settings.proxy.create') }}" class="btn btn-success">
            <i class="fe fe-plus"></i> @lang('Create new proxy')
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-9">

        @if($data->count() > 0)
            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead>
                            <tr>
                                <th>@lang('Server')</th>
                                <th>@lang('Country')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Use count')</th>
                                <th class="text-right">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('settings.proxy.edit', $item) }}">{{ $item->server }}</a>
                                    <div class="small text-muted">
                                        @lang('Expires in') {{ optional($item->expires_at)->diffForHumans() ?? __('(never)') }}
                                    </div>
                                </td>
                                <td>
                                    {{ config('countries.' . $item->country) }}
                                </td>
                                <td>
                                    @if($item->is_active)
                                        <span class="status-icon bg-success"></span> @lang('Active')
                                    @else
                                        <span class="status-icon bg-secondary"></span> @lang('Not active')
                                    @endif
                                </td>
                                <td>
                                    <span class="tag">{{ $item->use_count }}</span>
                                </td>
                                <td class="text-right">
                                    <form method="post" action="{{ route('settings.proxy.destroy', $item) }}" onsubmit="return confirm('@lang('Confirm delete?')');">
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
            </div>
        @endif

        {{ $data->appends( Request::all() )->links() }}

        @if($data->count() == 0)
            <div class="alert alert-primary text-center">
                <i class="fe fe-alert-triangle mr-2"></i> @lang('No proxies found')
            </div>
        @endif

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop