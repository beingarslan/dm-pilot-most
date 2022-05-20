@extends('layouts.app')

@section('title', __('RSS'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            @lang('RSS')
        </h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('RSS Feeds')</h3>
            <div class="card-options">
                <a href="{{ route('rss.create', $account) }}" class="btn btn-success">
                    <i class="fe fe-plus"></i> @lang('Add RSS')
                </a>
            </div>
        </div>

        @if($data->count() > 0)
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>@lang('Name')</th>
                        <th>@lang('Parsed items')</th>
                        <th>@lang('Status')</th>
                        <th class="text-right">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $rss)
                    <tr>
                        <td>
                            <a href="{{ route('rss.edit', [$account, $rss]) }}">{{ $rss->name }}</a>
                            <div class="small text-muted">
                                @lang('Added: :time', ['time' => $rss->created_at->format('M j, Y')])
                            </div>
                        </td>
                        <td>
                            <span class="tag tag-blue">{{ $rss->items_count }}</span>
                        </td>
                        <td>
                            @if($rss->is_active)
                                <span class="status-icon bg-success"></span> @lang('Active')
                            @else
                                <span class="status-icon bg-secondary"></span> @lang('Not active')
                            @endif
                        </td>
                        <td class="text-right">
                            <form method="post" action="{{ route('rss.destroy', [$account, $rss]) }}" onsubmit="return confirm('@lang('Confirm delete?')');">
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
            <i class="fe fe-alert-triangle mr-2"></i> @lang('No RSS found')
        </div>
    @endif

@stop