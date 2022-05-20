@extends('layouts.app')

@section('title', __('Update users list'))

@section('content')
<form role="form" method="post" action="{{ route('list.update', [$type, $list]) }}" autocomplete="off" class="repeater">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('Update users list')</h3>
        </div>
        <div class="card-body">

            <div class="form-group">
                <label class="form-label">@lang('List name')</label>
                <input type="text" name="name" value="{{ $list->name }}" class="form-control" placeholder="@lang('List name')">
            </div>
            <div class="row mb-3">
                <div class="col-md-8">
                    <small class="help-block">@lang('Instagram username without @ symbol')</small>
                </div>
                <div class="col-md-4 text-right">
                    <button data-repeater-create type="button" class="btn btn-sm btn-primary">
                        <i class="fe fe-plus"></i> @lang('Add user')
                    </button>
                </div>
            </div>
            <div data-repeater-list="items">
                @foreach($list->items as $item)
                <div class="form-group" data-repeater-item>
                    <div class="row align-items-center">
                        <div class="col-md-10">
                            <input type="text" name="text" value="{{ $item->text }}" class="form-control" placeholder="@lang('Username')">
                        </div>
                        <div class="col-md-2 text-right">
                            <button data-repeater-delete type="button" class="btn btn-sm btn-secondary btn-clean">
                                <i class="fe fe-trash"></i> @lang('Delete')
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex">
                <a href="{{ route('list.index', $type) }}" class="btn btn-secondary">@lang('Cancel')</a>
                <button class="btn btn-blue ml-auto">@lang('Update list')</button>
            </div>
        </div>
    </div>
</form>
@stop