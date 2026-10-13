<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\Role;
use App\Models\User;
use App\Support\Brand;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('manage-card', fn (User $user, Card $card) => $user->isAdmin() || $card->reseller_id === $user->id);
        foreach (array_keys(Role::PERMISSIONS) as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }

        View::composer(['card.activate', 'card.activated', 'card.inactive'], function ($view) {
            $view->with('brand', Brand::forCard($view->getData()['card'] ?? null));
        });
    }
}
