<?php

namespace App\Providers;

use App\Models\ContentPage;
use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views'));

        Gate::policy(Role::class, RolePolicy::class);

        View::composer('layouts.storefront', function ($view): void {
            $view->with('footerPages', ContentPage::published()->orderBy('title')->limit(6)->get());
        });

        if ($this->app->environment('production') && config('app.force_https')) {
            URL::forceScheme('https');
        }
    }
}
