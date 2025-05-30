<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Policies\BusinessSettingsPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * O array de mapeamento das policies da aplicação.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Registar quaisquer serviços de autenticação/autorização.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('manage', [BusinessSettingsPolicy::class, 'manage']);
    }
}
