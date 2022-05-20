<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-6 col-md-4 col-lg-2">
                <ul class="list-unstyled mb-2">
                    <li><i class="fe fe-home"></i> <a href="{{ route('dashboard') }}">@lang('Dashboard')</a></li>
                    <li><i class="fe fe-instagram"></i> <a href="{{ route('account.index') }}">@lang('Accounts')</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <ul class="list-unstyled mb-2">
                    @can('bot')
                    <li><i class="fe fe-message-square"></i> <a href="{{ route('bot.setup') }}">@lang('Chat Bot')</a></li>
                    @endcan
                    @can('rss')
                    <li><i class="fe fe-rss"></i> <a href="{{ route('rss.setup') }}">@lang('RSS')</a></li>
                    @endcan
                </ul>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <ul class="list-unstyled mb-2">
                    @can('send-message')
                    <li><i class="fe fe-send"></i> <a href="{{ route('dm.message') }}">@lang('Send message')</a></li>
                    @endcan
                    @can('autopilot')
                    <li><i class="fe fe-play"></i> <a href="{{ route('autopilot.index') }}">@lang('Autopilot')</a></li>
                    @endcan
                </ul>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                @can('lists')
                <ul class="list-unstyled mb-2">
                    <li><i class="fe fe-message-square"></i> <a href="{{ route('list.index', 'messages') }}">@lang('Messages lists')</a></li>
                    <li><i class="fe fe-users"></i> <a href="{{ route('list.index', 'users') }}">@lang('Users lists')</a></li>
                </ul>
                @endcan
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <ul class="list-unstyled mb-2">
                    @can('autopilot')
                    <li><i class="fe fe-edit-2"></i> <a href="{{ route('post.index') }}">@lang('Posts')</a></li>
                    @endcan
                    @can('direct-messenger')
                    <li><i class="fe fe-message-circle"></i> <a href="{{ route('direct.index') }}">@lang('Direct Messenger')</a></li>
                    @endcan
                </ul>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <ul class="list-unstyled mb-2">
                    @can('messages-log')
                    <li><i class="fe fe-eye"></i> <a href="{{ route('log.view') }}">@lang('Messages log')</a></li>
                    @endcan
                    @can('admin')
                    <li><i class="fe fe-shield"></i> <a href="{{ route('settings.index') }}">@lang('Administrator')</a></li>
                    @endcan
                </ul>
            </div>
        </div>
    </div>
</div>