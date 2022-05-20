@extends('layouts.app')

@section('title', __('Add RSS Feed'))

@section('content')

    <form role="form" method="post" action="{{ route('rss.store', $account) }}" autocomplete="off">
        @csrf
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">@lang('Setup new RSS Feed')</h3>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Name')</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="@lang('Name')">
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('URL')</label>
                            <input type="text" name="url" value="{{ old('url') }}" class="form-control {{ $errors->has('url') ? 'is-invalid' : '' }}" placeholder="@lang('URL')">
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Status')</label>
                            <label class="custom-switch">
                                <input type="checkbox" name="is_active" value="1" class="custom-switch-input" {{ old('is_active') ? 'checked' : '' }}>
                                <span class="custom-switch-indicator"></span>
                                <span class="custom-switch-description">@lang('Active')</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Location')</label>
                            <select name="location" class="form-control location-lookup"></select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Caption template')</label>
                            <textarea rows="3" name="template" class="form-control {{ $errors->has('template') ? 'is-invalid' : '' }}" placeholder="@lang('Caption template to post')" data-emojiable="true">{{ old('template') }}</textarea>
                            <div class="pt-3">
                                <input type="text" name="first_comment" value="{{ old('first_comment') }}" class="form-control" placeholder="@lang('Write your first comment')" data-emojiable="true">
                            </div>
                            <small class="help-block">@lang('Use placeholders: <strong>:title</strong> and <strong>:url</strong> to replace item Title and URL accordingly.', ['title' => ':title', 'url' => ':url'])</small>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <div class="d-flex">
                    <a href="{{ route('rss.index', $account) }}" class="btn btn-secondary">@lang('Cancel')</a>
                    <button class="btn btn-success ml-auto">@lang('Add RSS')</button>
                </div>
            </div>
        </form>
    </div>
@stop