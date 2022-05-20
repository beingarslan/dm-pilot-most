<div class="d-flex">
    <a class="header-brand" href="{{ route('dashboard') }}">
        <img src="{{ asset(config('pilot.LOGO.BACKEND')) }}" class="header-brand-img" alt="DM Pilot">
    </a>
    <div class="d-flex order-lg-2 ml-auto">

        @can('admin')
        <div class="nav-item  d-md-flex">
            <a href="{{ route('settings.index') }}" class="btn btn-outline-primary btn-sm text-nowrap {{ active('settings.*') }}">
                <i class="fe fe-shield"></i> @lang('Administrator')
            </a>
        </div>
        @endcan

        @if($notifications->count())
        <div class="dropdown d-none d-md-flex">
            <a class="nav-link icon" data-toggle="dropdown">
                <i class="fe fe-bell"></i>
                <span class="nav-unread"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                @foreach($notifications as $notification)
                <a href="{{ route('notifications') }}" class="dropdown-item">
                    {!! __('pilot.notification_' . $notification->data['action'], $notification->data) !!}
                    <div class="small text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                </a>
                @endforeach
                <div class="dropdown-divider"></div>
                <a href="{{ route('notifications') }}" class="dropdown-item text-center">@lang('View all notifications')</a>
            </div>
        </div>
        @endif

        <div class="dropdown">
            <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
                <span class="avatar avatar-blue avatar-shortname mr-3 d-lg-none"><div>{{ Auth::user()->name }}</div></span>
                <span class="d-none d-lg-block">
                    <span class="text-default">{{ Auth::user()->name }}</span>
                    <small class="text-muted d-block mt-1">{{ Auth::user()->email }}</small>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                <a class="dropdown-item {{ active('profile.index') }}" href="{{ route('profile.index') }}">
                    <i class="dropdown-icon fe fe-user"></i> @lang('Profile')
                </a>
                <a class="dropdown-item {{ active('billing.index') }}" href="{{ route('billing.index') }}">
                    <i class="dropdown-icon fe fe-credit-card"></i> @lang('Billing')
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('logout') }}">
                    <i class="dropdown-icon fe fe-log-out"></i> @lang('Sign out')
                </a>
            </div>
        </div>
    </div>
    <a href="#" class="header-toggler d-lg-none ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
        <span class="header-toggler-icon"></span>
    </a>
</div>