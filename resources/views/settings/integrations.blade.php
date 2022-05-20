@extends('layouts.app')

@section('title', __('Integrations'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Integrations')</h1>
</div>

<div class="row">
    <div class="col-md-9">

        <form role="form" method="post" action="{{ route('settings.update', 'integrations') }}" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="card">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#tab_payments" data-toggle="tab">
                            <i class="fe fe-credit-card mr-2"></i> @lang('Payments')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tab_social" data-toggle="tab">
                            <i class="fe fe-facebook mr-2"></i> @lang('Social login')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tab_miscellaneous" data-toggle="tab">
                            <i class="fe fe-info mr-2"></i> @lang('Miscellaneous')
                        </a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_payments">

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('PayPal')</h4></div>
                                <div><img src="{{ asset('public/img/paypal.svg') }}" height="60" alt="PayPal"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Environment')</label>
                                        <select name="settings[PAYPAL_SANDBOX]" class="form-control">
                                            <option value="0" {{ config('services.paypal.sandbox') == false ? 'selected' : '' }}>@lang('Live')</option>
                                            <option value="1" {{ config('services.paypal.sandbox') == true ? 'selected' : '' }}>@lang('Sandbox')</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">@lang('Client ID')</label>
                                        <input type="text" name="settings[PAYPAL_CLIENT_ID]" value="{{ config('services.paypal.client_id') }}" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">@lang('Secret')</label>
                                        <input type="text" name="settings[PAYPAL_SECRET]" value="{{ config('services.paypal.secret') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <ol>
                                        <li>@lang('Go to: <a href="https://developer.paypal.com" target="_blank">PayPal developer center</a>')</li>
                                        <li>@lang('Login into your account with your regular PayPal login credentials')</li>
                                        <li>@lang('Click <strong>Dashboard</strong> link on the top right')</li>
                                        <li>@lang('Scroll down to the <strong>REST API apps</strong> section and  click <strong>Create App</strong> button.')</li>
                                        <li>@lang('Include any name for your App and click <strong>Create App</strong> button')</li>
                                        <li>@lang('On the next page switch to <strong>Live</strong> mode and copy your Client ID and Client Secret.')</li>
                                        <li>@lang('Include Client ID and Secret that you\'ve copied in step 6.')</li>
                                    </ol>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Stripe')</h4></div>
                                <div>
                                    <img src="{{ asset('public/img/stripe.svg') }}" height="60" alt="Stripe">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Publishable key')</label>
                                        <input type="text" name="settings[STRIPE_KEY]" value="{{ config('services.stripe.key') }}" class="form-control" placeholder="pk_XXX">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">@lang('Secret key')</label>
                                        <input type="text" name="settings[STRIPE_SECRET]" value="{{ config('services.stripe.secret') }}" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">@lang('Webhook secret')</label>
                                        <input type="text" name="settings[STRIPE_WEBHOOK_SECRET]" value="{{ config('services.stripe.webhook.secret') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>@lang('Stripe integration allows you to accept payments from your users for using your service.')</p>
                                    <p>@lang('After setting-up Stripe, your users will be able to make a payment with their bank cards.')</p>
                                    <p>@lang('To enable Stripe integration you just need Stripe Publishable Key and Secret Key.')</p>
                                    <p>@lang('Your webhook URL is:') <a href="{{ route('gateway.notify', 'stripe') }}" target="_blank">{{ route('gateway.notify', 'stripe') }}</a></p>
                                    <p>@lang('Event to send: <code>:event</code>', ['event' => 'invoice.payment_succeeded'])</p>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Yandex.Kassa')</h4></div>
                                <div><img src="{{ asset('public/img/yandex.svg') }}" height="60" alt="Yandex"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Shop ID')</label>
                                        <input type="text" name="settings[YANDEX_SHOP_ID]" value="{{ config('services.yandex.shop_id') }}" class="form-control" placeholder="">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">@lang('Secret key')</label>
                                        <input type="text" name="settings[YANDEX_SECRET_KEY]" value="{{ config('services.yandex.secret_key') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>@lang('Yandex.Kassa integration allows you to accept payments from your users mostly from Russian Federation customers for using your service.')</p>
                                    <p>@lang('More details available at: <a href=":link" target="_blank">:link</a>', ['link' => 'https://kassa.yandex.ru'])</p>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Instamojo')</h4></div>
                                <div><img src="{{ asset('public/img/instamojo.svg') }}" height="60" alt="Instamojo"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Environment')</label>
                                        <select name="settings[INSTAMOJO_TEST_MODE]" class="form-control">
                                            <option value="0" {{ config('services.instamojo.test_mode') == false ? 'selected' : '' }}>@lang('Live')</option>
                                            <option value="1" {{ config('services.instamojo.test_mode') == true ? 'selected' : '' }}>@lang('Test mode')</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">@lang('Private API Key')</label>
                                        <input type="text" name="settings[INSTAMOJO_API_KEY]" value="{{ config('services.instamojo.api_key') }}" class="form-control" placeholder="">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">@lang('Private Auth Token')</label>
                                        <input type="text" name="settings[INSTAMOJO_AUTH_TOKEN]" value="{{ config('services.instamojo.auth_token') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>@lang('Multi-Channel Payment Gateway for India and fastest growing on-demand payments and e-commerce platform.')</p>
                                    <p>@lang('More details available at: <a href=":link" target="_blank">:link</a>', ['link' => 'https://www.instamojo.com'])</p>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Tinkoff')</h4></div>
                                <div><img src="{{ asset('public/img/tinkoff.svg') }}" height="60" alt="Tinkoff"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Terminal Key')</label>
                                        <input type="text" name="settings[TINKOFF_TERMINAL_KEY]" value="{{ config('services.tinkoff.terminal_key') }}" class="form-control" placeholder="">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">@lang('Secret Key')</label>
                                        <input type="text" name="settings[TINKOFF_SECRET_KEY]" value="{{ config('services.tinkoff.secret_key') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>@lang('Tinkoff integration allows you to accept payments from your users mostly from Russian Federation customers for using your service.')</p>
                                    <p>@lang('More details available at: <a href=":link" target="_blank">:link</a>', ['link' => 'https://oplata.tinkoff.ru'])</p>
                                    <p>@lang('Your webhook URL is:') <a href="{{ route('gateway.notify', 'tinkoff') }}" target="_blank">{{ route('gateway.notify', 'tinkoff') }}</a></p>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Paystack')</h4></div>
                                <div>
                                    <img src="{{ asset('public/img/paystack.svg') }}" height="60" alt="Paystack">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Secret key')</label>
                                        <input type="text" name="settings[PAYSTACK_SECRET]" value="{{ config('services.paystack.secret') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>@lang('Paystack integration allows you to accept payments from your users mostly from Africa customers for using your service.')</p>
                                    <p>@lang('More details available at: <a href=":link" target="_blank">:link</a>', ['link' => 'https://paystack.com'])</p>
                                    <p>@lang('To enable Paystack integration you just need Secret Key.')</p>
                                    <p>@lang('Your Webhook URL is:') <a href="{{ route('gateway.notify', 'paystack') }}" target="_blank">{{ route('gateway.notify', 'paystack') }}</a></p>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane" id="tab_social">

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Login with Facebook')</h4></div>
                                <div><img src="{{ asset('public/img/facebook.svg') }}" height="60" alt="Facebook"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('App ID')</label>
                                        <input type="text" name="settings[FACEBOOK_CLIENT_ID]" value="{{ config('services.facebook.client_id') }}" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">@lang('App Secret')</label>
                                        <input type="text" name="settings[FACEBOOK_CLIENT_SECRET]" value="{{ config('services.facebook.client_secret') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>@lang('Get your App ID and App Secret from:') <a href="https://developers.facebook.com" target="_blank">https://developers.facebook.com</a></p>
                                    <p>@lang('Valid OAuth Redirect URI:') <a href="{{ route('login.callback', 'facebook') }}" target="_blank">{{ route('login.callback', 'facebook') }}</a></p>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Login with Google')</h4></div>
                                <div><img src="{{ asset('public/img/google.svg') }}" height="60" alt="Google"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Client ID')</label>
                                        <input type="text" name="settings[GOOGLE_CLIENT_ID]" value="{{ config('services.google.client_id') }}" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">@lang('Client Secret')</label>
                                        <input type="text" name="settings[GOOGLE_CLIENT_SECRET]" value="{{ config('services.google.client_secret') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>@lang('Create a project:') <a href="https://console.developers.google.com/projectcreate" target="_blank">https://console.developers.google.com/projectcreate</a></p>
                                    <p>@lang('Create OAuth client ID credentials:') <a href="https://console.developers.google.com/apis/credentials" target="_blank">https://console.developers.google.com/apis/credentials</a></p>
                                    <p>@lang('Valid OAuth Redirect URI:') <a href="{{ route('login.callback', 'google') }}" target="_blank">{{ route('login.callback', 'google') }}</a></p>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane" id="tab_miscellaneous">

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Google Analytics')</h4></div>
                                <div><img src="{{ asset('public/img/google_analytics.svg') }}" height="60" alt="Google Analytics"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Property ID')</label>
                                        <input type="text" name="settings[GOOGLE_ANALYTICS]" value="{{ config('pilot.GOOGLE_ANALYTICS') }}" class="form-control" placeholder="UA-XXXXX-Y">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>@lang('Leave this field empty if you don\'t want to enable Google Analytics')</p>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Google reCaptcha')</h4></div>
                                <div><img src="{{ asset('public/img/google_recaptcha.svg') }}" height="60" alt="Google reCaptcha"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Site key')</label>
                                        <input type="text" name="settings[RECAPTCHA_SITE_KEY]" value="{{ config('recaptcha.api_site_key') }}" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">@lang('Secret key')</label>
                                        <input type="text" name="settings[RECAPTCHA_SECRET_KEY]" value="{{ config('recaptcha.api_secret_key') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>@lang('To protect your registration form, you can use Google reCaptcha service.')</p>
                                    <ul>
                                        <li>@lang('Get your free credentials from <a href=":link" target="_blank">:link</a>', ['link' => 'https://www.google.com/recaptcha/admin'])</li>
                                        <li>@lang('Select "reCAPTCHA v2" as a site key type.')</li>
                                        <li>@lang('Copy & paste the site and secret keys')</li>
                                    </ul>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-center justify-content-between">
                                <div><h4>@lang('Mailchimp')</h4></div>
                                <div><img src="{{ asset('public/img/mailchimp.svg') }}" height="60" alt="Mailchimp"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('API key')</label>
                                        <input type="text" name="settings[MAILCHIMP_APIKEY]" value="{{ config('newsletter.apiKey') }}" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">@lang('Audience ID')</label>
                                        <input type="text" name="settings[MAILCHIMP_LIST_ID]" value="{{ config('newsletter.lists.subscribers.id') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">

                                </div>
                            </div>

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

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop