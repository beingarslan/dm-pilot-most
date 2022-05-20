@extends('layouts.app')

@section('title', __('Update messages list'))

@section('content')
<form role="form" method="post" action="{{ route('list.update', [$type, $list]) }}" autocomplete="off" class="repeater">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('Update messages list')</h3>
        </div>
        <div class="card-body">

            <div class="form-group">
                <label class="form-label">@lang('List name')</label>
                <input type="text" name="name" value="{{ $list->name }}" class="form-control" placeholder="@lang('List name')">
            </div>
            <div class="row mb-3">
                <div class="col-md-8">
                    <small class="help-block">@lang('We also support Spintax. Feel free to use it like: {Hi|Hello|Hey} dear friend! {Thank you|We appreciate you} for your interest.')</small>
                </div>
                <div class="col-md-4 text-right">
                    <button data-repeater-create type="button" class="btn btn-sm btn-primary">
                        <i class="fe fe-plus"></i> @lang('Add message')
                    </button>
                </div>
            </div>
            <div data-repeater-list="items">
                @foreach($list->items as $item)
                <div class="form-group" data-repeater-item>
                    <label class="form-label">@lang('Message')</label>
                    <textarea name="text" rows="3" class="form-control" placeholder="@lang('Message text')" data-emojiable="true">{{ $item->text }}</textarea>
                    <div class="text-right mt-2">
                        <button data-repeater-delete type="button" class="btn btn-sm btn-secondary btn-clean">
                            <i class="fe fe-trash"></i> @lang('Delete')
                        </button>
                    </div>
                </div>
                @endforeach

                @if($list->items->count() == 0)
                <div class="form-group" data-repeater-item>
                    <label class="form-label">@lang('Message')</label>
                    <textarea name="text" rows="3" class="form-control" placeholder="@lang('Message text')" data-emojiable="true"></textarea>
                    <div class="text-right mt-2">
                        <button data-repeater-delete type="button" class="btn btn-sm btn-secondary btn-clean">
                            <i class="fe fe-trash"></i> @lang('Delete')
                        </button>
                    </div>
                </div>
                @endif
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