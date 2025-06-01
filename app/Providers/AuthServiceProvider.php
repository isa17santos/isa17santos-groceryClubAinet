<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Policies\BusinessSettingsPolicy;
use App\Models\Order;
use App\Policies\OrderPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\CardPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * O array de mapeamento das policies da aplicação.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Order::class => OrderPolicy::class,
    ];

    /**
     * Registar quaisquer serviços de autenticação/autorização.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('manage', [BusinessSettingsPolicy::class, 'manage']);
        Gate::define('viewPendingOrders', [OrderPolicy::class, 'viewPending']);
        Gate::define('access-inventory', [InventoryPolicy::class, 'access']);
        Gate::define('access-card', [CardPolicy::class, 'access']);
        Gate::define('manageUsers', [UserPolicy::class, 'manageUsers']);
    }
}
