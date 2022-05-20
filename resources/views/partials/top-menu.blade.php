<ul class="nav nav-tabs border-0 flex-column flex-lg-row">
    <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="fe fe-home"></i> @lang('Dashboard')
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('account.index') }}" class="nav-link {{ active('account.*') }}">
            <i class="fe fe-instagram"></i> @lang('Accounts')
        </a>
    </li>
    @can('lists')
    <li class="nav-item">
        <a href="javascript:void(0)" class="nav-link {{ active('list.*') }}" data-toggle="dropdown">
            <i class="fe fe-list"></i> @lang('Lists')
        </a>
        <div class="dropdown-menu dropdown-menu-arrow">
            <a href="{{ route('list.index', 'messages') }}" class="dropdown-item {{ active( route('list.index', 'messages') ) }}">
                <i class="fe fe-message-square"></i> @lang('Messages')
            </a>
            <a href="{{ route('list.index', 'users') }}" class="dropdown-item {{ active( route('list.index', 'users') ) }}">
                <i class="fe fe-users"></i> @lang('Users')
            </a>
        </div>
    </li>
    @endcan
    @can('send-message')
    <li class="nav-item">
        <a href="{{ route('dm.message') }}" class="nav-link {{ active('dm.message') }}">
            <i class="fe fe-send"></i> @lang('Send message')
        </a>
    </li>
    @endcan
    @can('autopilot')
    <li class="nav-item">
        <a href="{{ route('autopilot.index') }}" class="nav-link {{ active('autopilot.*') }}">
            <i class="fe fe-play"></i> @lang('Autopilot')
        </a>
    </li>
    @endcan
    @can('direct-messenger')
    <li class="nav-item">
        <a href="{{ route('direct.index') }}" class="nav-link {{ active('direct.*') }}">
            <i class="fe fe-message-circle"></i> @lang('Direct Messenger')
        </a>
    </li>
    @endcan
    @can('posts')
    <li class="nav-item">
        <a href="{{ route('post.index') }}" class="nav-link {{ active('post.*') }}">
            <i class="fe fe-edit-2"></i> @lang('Posts')
        </a>
    </li>
    @endcan
    @can('bot')
    <li class="nav-item">
        <a href="{{ route('bot.setup') }}" class="nav-link {{ active('bot.*') }}">
            <i class="fe fe-message-square"></i> @lang('Chat Bot')
        </a>
    </li>
    @endcan
    @can('rss')
    <li class="nav-item">
        <a href="{{ route('rss.setup') }}" class="nav-link {{ active('rss.*') }}">
            <i class="fe fe-rss"></i> @lang('RSS')
        </a>
    </li>
    @endcan
    @can('media-manager')
    <li class="nav-item">
        <a href="{{ route('media.index') }}" class="nav-link {{ active('media.*') }}">
            <i class="fe fe-image"></i> @lang('Media manager')
        </a>
    </li>
    @endcan
</ul>