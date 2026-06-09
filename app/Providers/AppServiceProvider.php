<?php

namespace App\Providers;

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
        view()->composer('layouts.admin', function ($view) {
            $view->with('layoutProjects', \App\Models\Project::all());
            $view->with('layoutUnreadNotificationsCount', \App\Models\Notification::where('is_read', false)->count());
        });
    }
}
