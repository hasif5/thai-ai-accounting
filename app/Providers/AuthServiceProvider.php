<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define accountant gate
        Gate::define('accountant', function ($user) {
            return $user->role === 'accountant';
        });

        // Define auditor gate
        Gate::define('auditor', function ($user) {
            return $user->role === 'auditor';
        });
    }
}