@extends('layouts.app')

@section('title', __('Challenge required'))

@section('content')

    <form role="form" method="post" action="{{ route('account.challenge.choice.confirm', $account) }}" autocomplete="off">
        @csrf
        <input type="hidden" name="api_path" value="{{ Request::get('api_path') }}">

        <div class="row">
            <div class="col-md-8 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">@lang('Challenge required')</h3>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <div class="form-label">@lang('Confirm with one of the methods below:')</div>
                            <div class="custom-controls-stacked">
                                <label class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="choice" value="0" checked>
                                    <div class="custom-control-label">@lang('SMS')</div>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="choice" value="1">
                                    <div class="custom-control-label">@lang('E-mail')</div>
                                </label>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-muted small p-5">
                        <ol class="pl-3 m-0">
                            <li>@lang('My account is at least 14 days old')</li>
                            <li>@lang('I have access to the email address and phone number associated with the account')</li>
                            <li>@lang('I don\'t use third-party tools for this account')</li>
                            <li>@lang('My account is linked to my Facebook account')</li>
                            <li>@lang('Make sure that the content of your account does not violate the rules of work in Instagram')</li>
                        </ol>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex">
                            <a href="{{ route('account.index') }}" class="btn btn-secondary">@lang('Cancel')</a>
                            <button type="submit" class="btn btn-success ml-auto">@lang('Confirm')</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </form>

@stop