@extends('layouts.app')

@section('title', __('Create new page'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Create new page')</h1>
</div>

<div class="row">
    <div class="col-md-9">

        <form role="form" method="post" action="{{ route('settings.pages.store') }}">
            @csrf
            <div class="card">
                <ul class="nav nav-tabs">
                    @foreach($languages as $code => $language)
                    <li class="nav-item">
                        <a class="nav-link {{ ($code == $default_language ? 'active' : '') }}" id="{{ $code }}-tab" href="#tab_{{ $code }}" data-toggle="tab">{{ $language['native'] }}</a>
                    </li>
                    @endforeach
                </ul>
                <div class="card-body">
                    <div class="tab-content">
                        @foreach($languages as $code => $language)
                        <div class="tab-pane {{ ($code == $default_language ? 'active' : '') }}" id="tab_{{ $code }}">

                            <div class="form-group">
                                <label class="form-label">@lang('Title')</label>
                                <input type="text" name="{{ $code }}[title]" value="{{ old($code . '.title') }}" class="form-control" placeholder="@lang('Title') ({{ $language['native'] }})" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">@lang('Description')</label>
                                <textarea name="{{ $code }}[description]" rows="10" class="form-control trumbowyg-editor" placeholder="@lang('Description') ({{ $language['native'] }})">{!! old($code . '.description') !!}</textarea>
                            </div>

                        </div>
                        @endforeach

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label">@lang('Slug')</label>
                                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="@lang('Slug')" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">@lang('Status')</label>
                                    <select name="is_active" class="form-control">
                                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>@lang('Published')</option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>@lang('Hidden')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <a href="{{ route('settings.pages.index') }}" class="btn btn-secondary">@lang('Cancel')</a>
                        <button class="btn btn-success ml-auto">@lang('Add page')</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop

@push('head')
<link rel="stylesheet" href="{{ asset('public/trumbowyg/ui/trumbowyg.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('public/trumbowyg/trumbowyg.min.js') }}"></script>
<script type="text/javascript">
    $(function(){
        $('.trumbowyg-editor').trumbowyg({
            btns: [
                ['formatting'],
                ['strong', 'em', 'del'],
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat'],
                ['fullscreen']
            ]
        });
    });
</script>
@endpush