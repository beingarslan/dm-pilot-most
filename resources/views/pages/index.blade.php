@extends('layouts.app')

@section('title', __('Pages manager'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Pages manager')</h1>
    <div class="page-options">
        <a href="{{ route('settings.pages.create') }}" class="btn btn-success">
            <i class="fe fe-plus"></i> @lang('Create new page')
        </a>
    </div>
</div>

<form method="get" action="{{ route('settings.pages.index') }}" autocomplete="off">
    <div class="row">
        <div class="col-9 col-md-9 col-lg-4">
            <div class="form-group">
                <input type="text" name="search" value="{{ Request::get('search') }}" class="form-control" placeholder="@lang('Search')">
            </div>
        </div>
        <div class="col-3 col-md-3 col-lg-8">
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
                                <th>@lang('Title')</th>
                                <th>@lang('Description')</th>
                                <th class="text-right">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('settings.pages.edit', $item) }}">{{ $item->title }}</a>
                                </td>
                                <td>
                                    {{ $item->description }}
                                </td>
                                <td class="text-right">
                                    <form method="post" action="{{ route('settings.pages.destroy', $item) }}" onsubmit="return confirm('@lang('Confirm delete?')');">
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
                <i class="fe fe-alert-triangle mr-2"></i> @lang('No pages found')
            </div>
        @endif

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop