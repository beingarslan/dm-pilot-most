@extends('layouts.app')

@section('title', __('Users lists'))

@section('content')
    <form method="get" action="{{ route('list.index', $type) }}" autocomplete="off">
        <div class="row">
            <div class="col-md-7">
                <h1 class="page-title">
                    @lang('Users lists')
                </h1>
            </div>
            <div class="col-9 col-md-4">
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
            <h3 class="card-title">@lang('Users lists')</h3>
            <div class="card-options">
                <a href="{{ route('list.create', $type) }}" class="btn btn-success">
                    <i class="fe fe-plus"></i> @lang('Create new list')
                </a>
            </div>
        </div>

        @if($data->count() > 0)
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>@lang('List name')</th>
                        <th>@lang('Users count')</th>
                        <th class="text-right">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td>
                            <a href="{{ route('list.edit', [$type, $item]) }}">{{ $item->name }}</a>
                            <div class="small text-muted">
                                @lang('Added: :time', ['time' => $item->created_at->format('M j, Y')])
                            </div>
                        </td>
                        <td>
                            <span class="tag">{{ $item->items_count }}</span>
                        </td>
                        <td class="text-right">
                            <form method="post" action="{{ route('list.destroy', [$type, $item]) }}" onsubmit="return confirm('@lang('Confirm delete?')');">
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
            <i class="fe fe-alert-triangle mr-2"></i> @lang('No users found')
        </div>
    @endif

@stop