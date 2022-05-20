@extends('layouts.app')

@section('title', __('Media manager'))

@section('content')
    <div class="card media-manager">
        <div class="card-header pl-3">
            <h3 class="card-title">@lang('Media')</h3>
            <small class="ml-3 text-gray">{{ $used_space }} / {{ $storage_limit }}</small>
            <div class="card-options">
                <button type="button" class="btn btn-danger btn-sm btn-clear mr-3">
                    <i class="fe fe-x"></i> <span class="d-none d-md-inline">{{ __('Delete all') }}</span>
                </button>
                <button type="button" class="btn btn-secondary btn-sm btn-delete mr-3" disabled>
                    <i class="fe fe-trash"></i> <span class="d-none d-md-inline">{{ __('Delete selected') }}</span>
                </button>
                <span class="btn btn-primary btn-sm btn-upload">
                    <i class="fe fe-upload"></i> <span class="d-none d-md-inline">@lang('Upload')</span>
                    <input type="file" name="files[]" data-url="{{ route('media.upload') }}" multiple />
                </span>
            </div>
        </div>
        <div class="p-3">
            <div class="dimmer active">
                <div class="loader"></div>
                <div class="dimmer-content">
                    <ul class="list-unstyled d-flex flex-wrap align-content-start media-files-container"></ul>
                </div>
            </div>
        </div>
    </div>
@stop