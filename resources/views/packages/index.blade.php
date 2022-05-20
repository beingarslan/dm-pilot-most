@extends('layouts.app')

@section('title', __('Packages'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Packages')</h1>
    <div class="page-options">
        <a href="{{ route('settings.packages.create') }}" class="btn btn-success">
            <i class="fe fe-plus"></i> @lang('Create new package')
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
                                <th>@lang('Title')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Visibility')</th>
                                <th class="text-right">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('settings.packages.edit', $item) }}">{{ $item->title }}</a>
                                    <div class="small text-muted">
                                        @lang('pilot.interval_' . $item->interval)
                                    </div>
                                </td>
                                <td>
                                    <span class="tag">{{ config('pilot.CURRENCY_SYMBOL') }}{{ $item->price }} {{ config('pilot.CURRENCY_CODE') }}</span>
                                </td>
                                <td>
                                    @if($item->is_hidden)
                                        <span class="status-icon bg-secondary"></span> @lang('Hidden')
                                    @else
                                        <span class="status-icon bg-success"></span> @lang('Visible')
                                    @endif
                                </td>
                                <td class="text-right">
                                    <form method="post" action="{{ route('settings.packages.destroy', $item) }}" onsubmit="return confirm('@lang('Confirm delete?')');">
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
                <i class="fe fe-alert-triangle mr-2"></i> @lang('No packages found')
            </div>
        @endif

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop