@extends('layouts.app')

@section('title', __('Direct Messenger'))

@section('content')

	@includeWhen($accounts->count() == 0, 'partials.no-accounts')

	@if($accounts->count())

		<div class="page-header">
	        <h1 class="page-title">
	            @lang('Direct Messenger')
	        </h1>
	        <div class="page-options">
	            <select id="account_id" class="form-control">
	                <option value="">&mdash; @lang('Select account') &mdash;</option>
	                @foreach($accounts as $account)
	                    <option value="{{ $account->id }}">{{ $account->username }}</option>
	                @endforeach
	            </select>
	        </div>
	    </div>

	    <div class="alert alert-primary text-center alert-no-account" role="alert">
	        @lang('Select account to start using Direct Messenger')
	    </div>

		<div class="row dm-container" style="display: none;">
			<div class="col-sm-12 col-md-4">

				<div class="card">
					<div class="card-header">
						<h3 class="card-title">@lang('Inbox') <sup class="unseen_count"><span class="badge badge-danger">2</span></sup></h3>
						<div class="card-options">
							<button class="btn btn-secondary btn-sm btn-reload">
								<i class="fe fe-refresh-ccw"></i>
							</button>
	                    </div>
					</div>
					<div class="dimmer" id="threads_list">
						<div class="loader"></div>
						<div class="dimmer-content">
							<div class="o-auto" style="height: 30rem;">
								<table class="table table-hover table-outline table-vcenter card-table">
									<tbody></tbody>
					            </table>
					            <div class="m-2 load-more-container">
						            <button class="btn btn-block btn-secondary btn-sm btn-load-more" style="display: none;">
									    <i class="fe fe-chevron-down"></i> @lang('Load more')
									</button>
					            </div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="col-sm-12 col-md-8">

				<div class="card">
					<div class="dimmer" id="messages_list">
						<div class="loader"></div>
						<div class="dimmer-content">

							<div class="o-auto" style="height: 30rem;">
								<ul class="list-group card-list-group"></ul>
							</div>
							<div class="card-header" style="padding: 0.5rem;">
								<div class="input-group">
									<input type="text" class="form-control message-text" placeholder="@lang('Message')" data-emojiable="true">
									<div class="input-group-append">
										<button type="button" class="btn btn-primary btn-send-message">
											<i class="fe fe-arrow-right"></i>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>

	@endif

@stop