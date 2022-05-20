@extends('layouts.app')

@section('title', __('Settings'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Settings')</h1>
</div>

<div class="row">
    <div class="col-md-9">

        <form role="form" method="post" action="{{ route('settings.update') }}" enctype="multipart/form-data" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fe fe-sliders mr-2"></i> @lang('General settings')</h3>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Site URL')</label>
                                <input type="text" name="settings[APP_URL]" value="{{ config('app.url') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Site name')</label>
                                <input type="text" name="settings[APP_NAME]" value="{{ config('app.name') }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">@lang('Description')</label>
                        <textarea name="settings[SITE_DESCRIPTION]" rows="2" class="form-control">{{ config('pilot.SITE_DESCRIPTION') }}</textarea>
                        <small class="help-block">@lang('Recommended length of the description is 150-160 characters')</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">@lang('Keywords')</label>
                        <textarea name="settings[SITE_KEYWORDS]" rows="3" class="form-control">{{ config('pilot.SITE_KEYWORDS') }}</textarea>
                    </div>

                    <hr>

                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-label">@lang('Favicon')</div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="logo_favicon">
                                    <label class="custom-file-label">@lang('Choose file')</label>
                                </div>
                                <small class="help-block">@lang('Recommended size: :size', ['size' => '48x48'])</small>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <img src="{{ asset(config('pilot.LOGO.FAVICON')) }}" alt="">
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-label">@lang('Back-end logotype')</div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="logo_backend">
                                    <label class="custom-file-label">@lang('Choose file')</label>
                                </div>
                                <small class="help-block">@lang('Recommended size: :size', ['size' => '110x32'])</small>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <img src="{{ asset(config('pilot.LOGO.BACKEND')) }}" alt="">
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-label">@lang('Front-end logotype')</div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="logo_frontend">
                                    <label class="custom-file-label">@lang('Choose file')</label>
                                </div>
                                <small class="help-block">@lang('Recommended size: :size', ['size' => '120x32'])</small>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <img src="{{ asset(config('pilot.LOGO.FRONTEND')) }}" alt="">
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-label">@lang('Mail logotype')</div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="logo_mail">
                                    <label class="custom-file-label">@lang('Choose file')</label>
                                </div>
                                <small class="help-block">@lang('Recommended size: :size', ['size' => '116x34'])</small>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <img src="{{ asset(config('pilot.LOGO.MAIL')) }}" alt="">
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Trial days')</label>
                                <input type="text" name="settings[TRIAL_DAYS]" value="{{ config('pilot.TRIAL_DAYS') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Trial number of accounts')</label>
                                <input type="text" name="settings[TRIAL_ACCOUNTS_COUNT]" value="{{ config('pilot.TRIAL_ACCOUNTS_COUNT') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Landing page skin')</label>
                                <select name="settings[SITE_SKIN]" class="form-control">
                                    @foreach($skins as $skin)
                                        <option value="{{ $skin }}" {{ $skin == config('pilot.SITE_SKIN') ? 'selected' : '' }}>{{ $skin }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Trial media storage size (MB)')</label>
                                <input type="text" name="settings[TRIAL_STORAGE]" value="{{ config('pilot.TRIAL_STORAGE') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">@lang('Trial messages limit per interval')</label>
                                <input type="text" name="settings[TRIAL_MESSAGES_COUNT]" value="{{ config('pilot.TRIAL_MESSAGES_COUNT') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4">

                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-switch">
                                    <input type="checkbox" name="settings[SYSTEM_PROXY]" value="1" class="custom-switch-input" {{ config('pilot.SYSTEM_PROXY') ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">@lang('Enable system proxy')</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="help-block">@lang('If you enable this option, system will try use most appropriate proxy from your proxy list while new account is being added.')</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-switch">
                                    <input type="checkbox" name="settings[CUSTOM_PROXY]" value="1" class="custom-switch-input" {{ config('pilot.CUSTOM_PROXY') ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">@lang('Users can add their own proxy address')</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="help-block">@lang('Allow users to use their own proxy address.')</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-switch">
                                    <input type="checkbox" name="settings[DISABLE_LANDING]" value="1" class="custom-switch-input" {{ config('pilot.DISABLE_LANDING') ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">@lang('Disable landing page')</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="help-block">@lang('If disabled, users will be redirected to the login page directly.')</small>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fe fe-save mr-2"></i> @lang('Save settings')
                    </button>
                </div>
            </div>

        </form>

        <div class="card card-collapsed">
            <div class="card-status card-status-left bg-blue"></div>
            <div class="card-header">
                <h3 class="card-title">@lang('Task scheduler')</h3>
                <div class="card-options">
                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                </div>
            </div>
            <div class="card-body">
                <p>@lang('Cron task should run once per minute to watch your accounts activity. You only need to add one cron entry to your server:')</p>

                <strong>@lang('General example:')</strong><br>
                <code>* * * * * /usr/bin/php {{ base_path('artisan') }} schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code>
                <br>
                <br>

                <p>@lang('If you are using MultiPHP Manager and selected specific version of the PHP try to use this command:')</p>

                <strong>@lang('Specific PHP version example:')</strong><br>
                <code>* * * * * /usr/local/bin/ea-php{{ PHP_MAJOR_VERSION . PHP_MINOR_VERSION }} {{ base_path('artisan') }} schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code>

                <hr>
                <p>@lang("If your server doesn't support cron task you can use external cron task services to call these URL's:")</p>

                <strong>@lang('Every minute:')</strong>

                <ul class="list-unstyled">
                    <li><a href="{{ route('cron.queue', 'autopilot') }}" target="_blank">{{ route('cron.queue', 'autopilot') }}</a></li>
                    <li><a href="{{ route('cron.queue', 'mail') }}" target="_blank">{{ route('cron.queue', 'mail') }}</a></li>
                    <li><a href="{{ route('cron.messages') }}" target="_blank">{{ route('cron.messages') }}</a></li>
                    <li><a href="{{ route('cron.posts') }}" target="_blank">{{ route('cron.posts') }}</a></li>
                    <li><a href="{{ route('cron.expired') }}" target="_blank">{{ route('cron.expired') }}</a></li>
                </ul>

                <strong>@lang('Every ten minutes:')</strong>

                <ul class="list-unstyled">
                    <li><a href="{{ route('cron.followers') }}" target="_blank">{{ route('cron.followers') }}</a></li>
                    <li><a href="{{ route('cron.following') }}" target="_blank">{{ route('cron.following') }}</a></li>
                </ul>

                <strong>@lang('Hourly:')</strong>

                <ul class="list-unstyled">
                    <li><a href="{{ route('cron.retry') }}" target="_blank">{{ route('cron.retry') }}</a></li>
                </ul>
            </div>
        </div>

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop