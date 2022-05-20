<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin', function ($user) {
            return $user->is_admin;
        });

        $permissions = Config::get('pilot.PERMISSIONS');

        if ($permissions) {
            foreach (array_keys($permissions) as $permission) {
                Gate::define($permission, function ($user) use ($permission) {
                    return $user->package->hasPermissionTo($permission);
                });
            }
        }
    }
}
