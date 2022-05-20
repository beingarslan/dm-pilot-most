@extends('layouts.app')

@section('title', __('New users list'))

@section('content')
<form role="form" method="post" action="{{ route('list.store', $type) }}" class="repeater">
    @csrf
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('New users list')</h3>
            <div class="card-options">
                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#addMultiple">
                    <i class="fe fe-user-plus"></i> @lang('Add multiple')
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">@lang('List name')</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="@lang('List name')">
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
                <div class="form-group" data-repeater-item>
                    <div class="row align-items-center">
                        <div class="col-md-10">
                            <input type="text" name="text" class="form-control" placeholder="@lang('Username')" autocomplete="off">
                        </div>
                        <div class="col-md-2 text-right">
                            <button data-repeater-delete type="button" class="btn btn-sm btn-secondary btn-clean">
                                <i class="fe fe-trash"></i> @lang('Delete')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex">
                <a href="{{ route('list.index', $type) }}" class="btn btn-secondary">@lang('Cancel')</a>
                <button class="btn btn-success ml-auto">@lang('Add list')</button>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="addMultiple" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form role="form" method="post" action="{{ route('list.multiple', $type) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add multiple')</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    @if($accounts->count())
                        <div class="row searchByHashtagForm">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Search by #hashtag')</label>
                                    <div class="input-group">
                                        <input type="text" name="q" class="form-control" placeholder="@lang('Search by #hashtag')">
                                        <span class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fe fe-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Account')</label>
                                    <select name="account_id" class="form-control">
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account') == $account->id ? 'selected' : '' }}>{{ $account->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr class="mt-2 mb-4">

                    @if($accounts->count())
                        <div class="row searchByAccountForm">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Search by @account')</label>
                                    <div class="input-group">
                                        <input type="text" name="q" class="form-control" placeholder="@lang('Search by @account')">
                                        <span class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fe fe-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Account')</label>
                                    <select name="account_id" class="form-control">
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account') == $account->id ? 'selected' : '' }}>{{ $account->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">@lang('Instagram usernames')</label>
                        <textarea rows="10" name="text" class="form-control"></textarea>
                        <small class="help-block">@lang('Import usernames by pasting list here. Each username should be on a new line. Usernames should be without @ symbol')</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('Parse and import')</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop