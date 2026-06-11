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
            if (session()->has('impersonating_client_id')) {
                $clientId = session('impersonating_client_id');
                $view->with('layoutProjects', \App\Models\Project::where('user_id', $clientId)->get());
                $view->with('layoutUnreadNotificationsCount', \App\Models\Notification::where('user_id', $clientId)->where('is_read', false)->count());
            } else {
                $view->with('layoutProjects', \App\Models\Project::where(function($q) {
                    $q->where('user_id', 1)
                      ->orWhereNull('user_id')
                      ->orWhereHas('user', function($qu) {
                          $qu->where('role', 'admin');
                      });
                })->get());
                $view->with('layoutUnreadNotificationsCount', \App\Models\Notification::where(function($q) {
                    $q->where('user_id', 1)
                      ->orWhereNull('user_id')
                      ->orWhereHas('user', function($qu) {
                          $qu->where('role', 'admin');
                      });
                })->where('is_read', false)->count());
            }
        });
    }
}
