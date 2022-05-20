@extends('layouts.install')

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Installation finished!</h3>
        </div>
        <div class="card-body">
            <p>Cron task should run once per minute to watch your accounts activity. You only need to add one cron entry to your server:</p>

            <strong>General example:</strong><br>
            <code>* * * * * /usr/bin/php {{ base_path('artisan') }} schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code>
            <br>
            <br>

            <p>If you are using MultiPHP Manager and selected specific version of the PHP try to use this command:</p>

            <strong>Specific PHP version example:</strong><br>
            <code>* * * * * /usr/local/bin/ea-php{{ PHP_MAJOR_VERSION . PHP_MINOR_VERSION }} {{ base_path('artisan') }} schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code>
        </div>
    </div>

    <form method="post" action="{{ route('install.finish') }}" autocomplete="off">
        @csrf

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add your system Administrator</h3>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Administrator full name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Full name" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Administrator E-mail</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="E-mail" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Administrator password</label>
                            <input type="password" name="password" value="" class="form-control" placeholder="Password" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm password</label>
                            <input type="password" name="password_confirmation" value="" class="form-control" placeholder="Confirm password" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Purchase Code')</label>
                            <input type="text" name="code" value="{{ old('code') }}" class="form-control" placeholder="********-****-****-****-************" maxlength="36" required>
                        </div>

                        <small class="text-muted">
                            <strong>@lang('How to find your purchase code:')</strong>
                            <ol>
                                <li>Log into your Envato Market account.</li>
                                <li>Hover the mouse over your username at the top of the screen.</li>
                                <li>Click &laquo;Downloads&raquo; from the drop-down menu.</li>
                                <li>Click &laquo;License certificate &amp; purchase code&raquo; (available as PDF or text file).</li>
                            </ol>
                        </small>
                    </div>
                </div>



            </div>
            <div class="card-footer">
                <button class="btn btn-block btn-primary">Finish installation</button>
            </div>
        </div>

    </form>

@endsection