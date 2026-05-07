<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('owner', fn($user) => $user->role === 'owner');
        Gate::define('inventory', fn($user) => in_array($user->role, ['owner', 'inventory']));
        Gate::define('kasir', fn($user) => in_array($user->role, ['owner', 'kasir']));
    }
}
