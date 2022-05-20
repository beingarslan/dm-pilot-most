@extends('layouts.app')

@section('title', __('Send message'))

@section('content')

	@includeWhen($accounts->count() == 0, 'partials.no-accounts')

	@if($accounts->count())
	    <div class="card">
		    <div class="card-header">
		        <h3 class="card-title">@lang('Send message')</h3>
		    </div>
		    <div class="card-body">
		    	<form role="form" method="post" action="{{ route('dm.message_send') }}" enctype="multipart/form-data" autocomplete="off" onsubmit="return confirm('@lang('Are you sure want to send this message?')');">
		        	@csrf

		        	<div class="row">
	                    <div class="col-md-4">
				            <div class="form-group">
				                <label class="form-label">@lang('Account')</label>
				                <select name="account_id" class="form-control">
				                    @foreach($accounts as $account)
				                        <option value="{{ $account->id }}" {{ old('account') == $account->id ? 'selected' : '' }}>{{ $account->username }}</option>
				                    @endforeach
				                </select>
				                <small class="help-block">@lang('Select your account')</small>
				            </div>
	                    </div>
	                    <div class="col-md-8">
				        	<div class="form-group">
				                <label class="form-label">@lang('Target audience')</label>

				                <div class="selectgroup w-100">
				                    <label class="selectgroup-item">
				                        <input type="radio" name="audience" value="1" class="selectgroup-input" {{ old('audience', 1) == '1' ? 'checked' : '' }}>
				                        <span class="selectgroup-button">@lang('Followers')</span>
				                    </label>

				                    <label class="selectgroup-item">
				                        <input type="radio" name="audience" value="2" class="selectgroup-input" {{ old('audience') == '2' ? 'checked' : '' }}>
				                        <span class="selectgroup-button">@lang('Following')</span>
				                    </label>

				                    <label class="selectgroup-item">
				                        <input type="radio" name="audience" value="3" class="selectgroup-input" {{ old('audience') == '3' ? 'checked' : '' }}>
				                        <span class="selectgroup-button">@lang('Users list')</span>
				                    </label>

				                    <label class="selectgroup-item">
				                        <input type="radio" name="audience" value="4" class="selectgroup-input" {{ old('audience') == '4' ? 'checked' : '' }}>
				                        <span class="selectgroup-button">@lang('Direct contacted')</span>
				                    </label>
				                </div>
				            </div>
	                    </div>
	                </div>

	                <div class="row">
	                    <div class="col-md-4">
				            <div class="form-group">
				                <div class="form-label">@lang('Message type')</div>
				                <div class="custom-controls-stacked">

				                    <label class="custom-control custom-radio">
				                    <input type="radio" class="custom-control-input" name="message_type" value="list" {{ old('message_type', 'list') == 'list' ? 'checked' : '' }}>
				                        <div class="custom-control-label">@lang('List of messages')</div>
				                    </label>

				                    <label class="custom-control custom-radio">
				                    <input type="radio" class="custom-control-input" name="message_type" value="text" {{ old('message_type') == 'text' ? 'checked' : '' }}>
				                        <div class="custom-control-label">@lang('Custom text')</div>
				                    </label>

				                    <label class="custom-control custom-radio">
				                    <input type="radio" class="custom-control-input" name="message_type" value="like" {{ old('message_type') == 'like' ? 'checked' : '' }}>
				                        <div class="custom-control-label">@lang('Like')</div>
				                    </label>

				                    <label class="custom-control custom-radio">
				                    <input type="radio" class="custom-control-input" name="message_type" value="photo" {{ old('message_type') == 'photo' ? 'checked' : '' }}>
				                        <div class="custom-control-label">@lang('Photo')</div>
				                    </label>

				                    <label class="custom-control custom-radio">
				                    <input type="radio" class="custom-control-input" name="message_type" value="video" {{ old('message_type') == 'video' ? 'checked' : '' }}>
				                        <div class="custom-control-label">@lang('Video')</div>
				                    </label>

				                    <label class="custom-control custom-radio">
				                    <input type="radio" class="custom-control-input" name="message_type" value="post" {{ old('message_type') == 'post' ? 'checked' : '' }}>
				                        <div class="custom-control-label">@lang('Post')</div>
				                    </label>
				                </div>
				            </div>
	                    </div>
	                    <div class="col-md-8">
	                    	<div class="form-group users_list" style="display: none;">
				                <label class="form-label">@lang('Users list')</label>
				                <select name="users_list_id" class="form-control {{ $errors->has('users_list_id') ? 'is-invalid' : '' }}">
				                    <option value=""></option>
				                    @foreach($users_lists as $ul)
				                        <option value="{{ $ul->id }}" {{ old('users_list_id') == $ul->id ? 'selected' : '' }}>{{ $ul->name }}</option>
				                    @endforeach
				                </select>
				            </div>

				            <div class="form-group options option_list">
				                <label class="form-label">@lang('Messages list')</label>
				                <select name="messages_list_id" class="form-control {{ $errors->has('messages_list_id') ? 'is-invalid' : '' }}">
				                    <option value=""></option>
				                    @foreach($messages_lists as $ml)
				                        <option value="{{ $ml->id }}" {{ old('messages_list_id') == $ml->id ? 'selected' : '' }}>{{ $ml->name }}</option>
				                    @endforeach
				                </select>
				            </div>

				            <div class="form-group options option_text" style="display: none;">
				                <label class="form-label">@lang('Text')</label>
					            <textarea rows="3" name="text" class="form-control {{ $errors->has('text') ? 'is-invalid' : '' }}" placeholder="@lang('Compose a message to be sent')" data-emojiable="true">{{ old('text') }}</textarea>
				                <small class="help-block">@lang('We also support Spintax. Feel free to use it like: {Hi|Hello|Hey} dear friend! {Thank you|We appreciate you} for your interest.')</small>
				            </div>

				            <div class="form-group options option_like" style="display: none;">
				                <div class="alert alert-info">
		                            <i class="fe fe-heart mr-2"></i> @lang('Like action will be sent')
		                        </div>
				            </div>

				            <div class="form-group options option_photo" style="display: none;">
				                <label class="form-label">@lang('Photo')</label>
				                <input type="file" name="photo" class="form-control {{ $errors->has('photo') ? 'is-invalid' : '' }}">
				                <small class="help-block">@lang('Only PNG, JPEG, JPG, GIF files supported.')</small>
				            </div>

				            <div class="form-group options option_video" style="display: none;">
				                <label class="form-label">@lang('Video')</label>
				                <input type="file" name="video" class="form-control {{ $errors->has('video') ? 'is-invalid' : '' }}">
				                <small class="help-block">@lang('Only MP4 (H.264) file supported.')</small>
				            </div>

				            <div class="options option_disappearing" style="display: none;">
								<label class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="disappearing">
									<span class="custom-control-label">@lang('Disappearing')</span>
								</label>
							</div>

							<div class="options option_post" style="display: none;">
								<input type="hidden" name="media_id" value="{{ old('media_id') }}">

								<div class="form-group">
									<label class="form-label">@lang('Post URL')</label>
									<div class="input-group">
										<input type="text" name="post_url" class="form-control {{ $errors->has('media_id') ? 'is-invalid' : '' }}" value="{{ old('post_url') }}">
										<span class="input-group-append">
											<button class="btn btn-success check_post" type="button">@lang('Check post')</button>
										</span>
									</div>
									<small class="help-block">@lang('Format: https://www.instagram.com/p/XXXXXXX/')</small>
								</div>

								<div class="form-group">
					                <label class="form-label">@lang('Text')</label>
					               	<textarea rows="3" name="post_text" class="form-control {{ $errors->has('post_text') ? 'is-invalid' : '' }}" placeholder="@lang('Compose a message to be sent')" data-emojiable="true">{{ old('post_text') }}</textarea>
					                <small class="help-block">@lang('We also support Spintax. Feel free to use it like: {Hi|Hello|Hey} dear friend! {Thank you|We appreciate you} for your interest.')</small>
					            </div>

								<div class="row" id="post_preview" style="display: none;">
									<div class="col-md-6">
										<div class="card ">
											<img src="" alt="" class="post_thumbnail">
											<div class="p-3">
												<div class="post_author_name"></div>
												<small class="post_title text-muted"></small>
											</div>
										</div>
									</div>
								</div>

							</div>

	                    </div>
	                </div>


		            <div class="form-footer">
		            	<div class="row">
		            		<div class="col-md-12 col-lg-6">
		            			<div class="alert alert-warning">
		                            <i class="fe fe-alert-triangle mr-2"></i> @lang('It\'s highly recommended to follow Instagram DM sending limits.')
		                        </div>
		            		</div>
		            		<div class="col-md-12 col-lg-6">
								<div class="form-group">
									<div class="input-group">
										<select name="speed" class="form-control">
											@foreach($message_speed as $speed => $title)
											<option value="{{ $speed }}" {{ old('speed') == $speed ? 'selected' : '' }}>@lang($title)</option>
											@endforeach
										</select>
										<span class="input-group-append">
											<button class="btn btn-primary" type="submit">@lang('Send message')</button>
										</span>
									</div>
								</div>
		            		</div>
		            	</div>
		            </div>

		        </form>
		    </div>
		</div>
	@endif

@stop

@push('scripts')
    <script type="text/javascript">
    $(function() {

        @if(old('message_type'))
            $('input[name="message_type"][value="{{ old('message_type') }}"]').trigger('change');
        @endif

        @if(old('audience'))
            $('input[name="audience"][value="{{ old('audience') }}"]').trigger('change');
        @endif

    });
    </script>
@endpush