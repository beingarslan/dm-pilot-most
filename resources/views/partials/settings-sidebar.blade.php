<div class="list-group list-group-transparent mb-3">
    <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.index') }}">
        <i class="fe fe-settings mr-2"></i> @lang('Settings')
    </a>
    <a href="{{ route('settings.localization') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.localization') }}">
        <i class="fe fe-volume-2 mr-2"></i> @lang('Localization')
    </a>
    <a href="{{ route('settings.email') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.email') }}">
        <i class="fe fe-mail mr-2"></i> @lang('E-mail Settings')
    </a>
    <a href="{{ route('settings.integrations') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.integrations') }}">
        <i class="fe fe-code mr-2"></i> @lang('Integrations')
    </a>
    <a href="{{ route('settings.users.index') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.users.*') }}">
        <i class="fe fe-users mr-2"></i> @lang('Users')
    </a>
    <a href="{{ route('settings.packages.index') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.packages.*') }}">
        <i class="fe fe-package mr-2"></i> @lang('Packages')
    </a>
    <a href="{{ route('settings.proxy.index') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.proxy.*') }}">
        <i class="fe fe-shield mr-2"></i> @lang('Proxies')
    </a>
    <a href="{{ route('settings.payments') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.payments') }}">
        <i class="fe fe-dollar-sign mr-2"></i> @lang('Payments')
    </a>
    <a href="{{ route('settings.pages.index') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.pages.*') }}">
        <i class="fe fe-file mr-2"></i> @lang('Pages manager')
    </a>
    <a href="{{ route('settings.upgrade.index') }}" class="list-group-item list-group-item-action d-flex align-items-center {{ active('settings.upgrade.index') }}">
        <i class="fe fe-refresh-ccw mr-2"></i> @lang('System upgrade')
    </a>
</div>

<div class="alert alert-info">
    @lang('DM Pilot version') {{ config('pilot.version') }}
</div>