@extends('layouts.app')

@section('title', __('Posts'))

@section('content')

    @includeWhen($accounts->count() == 0, 'partials.no-accounts')

    @if($accounts->count())

        <div class="page-header">
            <h1 class="page-title">
                @lang('Posts')
            </h1>
        </div>

        <form method="get" action="{{ route('post.index') }}" autocomplete="off">
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="form-group">
                        <select name="account" class="form-control">
                            <option value="">@lang('All accounts')</option>
                            @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ (Request::get('account') == $account->id ? 'selected' : '') }}>{{ $account->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2">
                    <div class="form-group">
                        <select name="type" class="form-control">
                            <option value="">@lang('All types')</option>
                            <option value="post" {{ (Request::get('type') == 'post' ? 'selected' : '') }}>@lang('Post')</option>
                            <option value="album" {{ (Request::get('type') == 'album' ? 'selected' : '') }}>@lang('Album')</option>
                            <option value="story" {{ (Request::get('type') == 'story' ? 'selected' : '') }}>@lang('Story')</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="">@lang('All statuses')</option>
                            <option value="1" {{ (Request::get('status') == config('pilot.POST_STATUS_SCHEDULED') ? 'selected' : '') }}>@lang('Scheduled')</option>
                            <option value="2" {{ (Request::get('status') == config('pilot.POST_STATUS_PUBLISHED') ? 'selected' : '') }}>@lang('Published')</option>
                            <option value="3" {{ (Request::get('status') == config('pilot.POST_STATUS_FAILED') ? 'selected' : '') }}>@lang('Failed')</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2">
                    <div class="form-group">
                        <select name="sort" class="form-control">
                            <option value="desc" {{ (Request::get('sort') == 'desc' ? 'selected' : '') }}>@lang('Newest first')</option>
                            <option value="asc" {{ (Request::get('sort') == 'asc' ? 'selected' : '') }}>@lang('Oldest first')</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 col-lg-2 text-right text-nowrap">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">
                            <i class="fe fe-filter"></i>
                        </button>

                        <a href="{{ route('post.create') }}" class="btn btn-success">
                            <i class="fe fe-plus"></i> @lang('New post')
                        </a>
                    </div>
                </div>
            </div>
        </form>

        @if($data->count() > 0)
            <div class="row row-cards">
                @foreach($data as $post)
                <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-2">
                                    <div class="avatar avatar-md dm-load-avatar" data-username="{{ $post->account->username }}"></div>
                                </div>
                                <div class="col-8">
                                    <div><a href="https://www.instagram.com/{{ $post->account->username }}" target="_blank" class="text-default"><strong>{{ $post->account->username }}</strong></a></div>
                                    @if($post->locationName)
                                        <small class="d-block text-muted text-truncate" title="{{ $post->locationName }}">
                                            {{ $post->locationName }}
                                        </small>
                                    @endif
                                </div>
                                <div class="col-2">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-secondary btn-clean" data-toggle="dropdown">
                                            <i class="fe fe-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @if($post->url)
                                                <a href="{{ $post->url }}" target="_blank" class="dropdown-item">@lang('Go to post on Instagram')</a>
                                            @endif

                                            @if($post->isPublished == false)
                                                <a href="{{ route('post.edit', $post) }}" class="dropdown-item">@lang('Edit post')</a>
                                            @endif

                                            <form method="post" action="{{ route('post.destroy', $post) }}" onsubmit="return confirm('@lang('Confirm delete?')');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item">
                                                    @lang('Delete post')
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($post->url)
                            <a href="{{ $post->url }}" target="_blank">
                                <img src="{{ $post->preview_image }}" alt="{{ $post->caption }}">
                            </a>
                        @else
                            <a href="{{ route('post.edit', $post) }}">
                                <img src="{{ $post->preview_image }}" alt="{{ $post->caption }}">
                            </a>
                        @endif

                        <div class="card-body d-flex flex-column p-3">
                            <div><a href="https://www.instagram.com/{{ $post->account->username }}" target="_blank" class="text-default"><strong>{{ $post->account->username }}</strong></a></div>

                            <small class="d-block dm-show-more">{{ $post->caption }}</small>

                            <div class="d-flex align-items-center pt-2">
                                <div>
                                    @if($post->isPublished)
                                        <small class="text-muted">
                                            {{ $post->posted_at->diffForHumans() }}
                                        </small>
                                    @endif

                                    @if($post->isScheduled)
                                        <small class="text-muted">
                                            {{ $post->scheduled_at->diffForHumans() }}
                                        </small>
                                    @endif
                                </div>
                                <div class="ml-auto">
                                    @if($post->isFailed)
                                        <span class="badge badge-danger">@lang('FAILED')</span>
                                    @endif

                                    @if($post->isScheduled)
                                        <span class="badge badge-primary">@lang('SCHEDULED')</span>
                                    @endif

                                    @if($post->isPublished)
                                        <span class="badge badge-success">@lang('PUBLISHED')</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
        </div>
        @endif

        @if($data->count() == 0)
            <div class="alert alert-primary text-center">
                <i class="fe fe-alert-triangle mr-2"></i> @lang('No posts found')
            </div>
        @endif

        {{ $data->appends( Request::all() )->links() }}

    @endif

@stop