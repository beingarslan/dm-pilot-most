@extends('layouts.app')

@section('title', __('Chat Bot'))

@section('content')
    <form role="form" method="post" action="{{ route('bot.update', $account) }}" autocomplete="off" class="qa-repeater">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">@lang('Chat Bot') &ndash; {{ $account->username }}</h3>
                <div class="card-options">
                    <button type="submit" name="restart" class="btn btn-secondary btn-sm mr-4">@lang('Restart')</button>

                    <label class="custom-switch m-0">
                        <input type="checkbox" name="is_active" value="1" class="custom-switch-input" {{ $bot->is_active ? 'checked' : '' }}>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">@lang('Active')</span>
                    </label>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Welcome text')</label>
                            <textarea rows="4" name="welcome_text" class="form-control" placeholder="@lang('Welcome text')" data-emojiable="true">{{ old('welcome_text', $bot->welcome_text) }}</textarea>
                            <small class="help-block">@lang('Welcome text once a new message received.')</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Help text')</label>
                            <textarea rows="4" name="unknown_text" class="form-control" placeholder="@lang('Help text')" data-emojiable="true">{{ old('welcome_text', $bot->unknown_text) }}</textarea>
                            <small class="help-block">@lang('If nothing matches, send this text as a helper to start over.')</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Notification e-mail')</label>
                            <input type="email" name="email" value="{{ $bot->email }}" class="form-control" placeholder="@lang('Notification e-mail')">
                            <small class="help-block">@lang('A transcript will be sent to this email once dialogue reaches the end.')</small>
                        </div>
                    </div>
                    <div class="col-md-6">

                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class=" ">
                        <strong>@lang('Dialogue: Questions &amp; Answers')</strong>
                    </div>
                    <div class="  text-right">
                        <button data-repeater-create type="button" class="btn btn-sm btn-primary">
                            <i class="fe fe-plus"></i> @lang('Add Q&amp;A')
                        </button>
                    </div>
                </div>
                <hr class="mt-3 mb-3">

                <div class="card bg-blue-lightest">
                    <div class="card-status card-status-left bg-blue"></div>
                    <div class="card-body pl-4 pr-4 pt-2 pb-2">
                        Chat Bot supports <strong>Spintax</strong>. You can also use the placeholders <strong>:username</strong> and <strong>:fullname</strong> &ndash; they will be replaced by the sender`s username and full name accordingly.
                    </div>
                </div>

                <div class="mb-2"><small class="help-block">@lang('You can sort Q&amp;A by draging them. If you expect any possible answer, please use * symbol.')</small></div>

                <ul class="list-unstyled dialogue-qa" data-repeater-list="items">
                    @if(count($bot->qa) == 0)
                    <li class="list-group-item pl-2 pr-2" data-repeater-item>
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <div class="pl-2 pr-2">
                                <i class="fe fe-move"></i>
                            </div>
                            <div class="flex-fill w-100 p-2">
                                <div class="form-group">
                                    <label class="form-label">@lang('Hears')</label>
                                    <input type="text" name="question" value="" class="form-control hears" placeholder="@lang('Start typing question')">
                                </div>
                            </div>
                            <div class="flex-fill w-100 p-2">
                                <div class="form-group">
                                    <label class="form-label">@lang('Replies')</label>
                                    <textarea rows="3" name="answer" class="form-control" placeholder="@lang('Replies this')"></textarea>
                                </div>
                            </div>
                            <div class="pl-2 pr-2">
                                <button data-repeater-delete type="button" class="btn btn-sm btn-secondary btn-clean">
                                    <i class="fe fe-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    @endif

                    @foreach($bot->qa as $qa)
                    <li class="list-group-item pl-2 pr-2" data-repeater-item>
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <div class="pl-2 pr-2">
                                <i class="fe fe-move"></i>
                            </div>
                            <div class="flex-fill w-100 p-2">
                                <div class="form-group">
                                    <label class="form-label">@lang('Hears')</label>
                                    <input type="text" name="question" value="{{ join(',', $qa->hears) }}" class="form-control hears" placeholder="@lang('Start typing question')">
                                </div>
                            </div>
                            <div class="flex-fill w-100 p-2">
                                <div class="form-group">
                                    <label class="form-label">@lang('Replies')</label>
                                    <textarea rows="3" name="answer" class="form-control" placeholder="@lang('Replies this')">{{ $qa->message['text'] }}</textarea>
                                </div>
                            </div>
                            <div class="pl-2 pr-2">
                                <button data-repeater-delete type="button" class="btn btn-sm btn-secondary btn-clean">
                                    <i class="fe fe-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>


            </div>
            <div class="card-footer">
                <div class="d-flex">
                    <a href="{{ route('bot.setup') }}" class="btn btn-secondary">@lang('Back')</a>
                    <button type="submit" name="save" class="btn btn-success ml-auto">@lang('Save changes')</button>
                </div>
            </div>
        </div>
    </form>
@stop