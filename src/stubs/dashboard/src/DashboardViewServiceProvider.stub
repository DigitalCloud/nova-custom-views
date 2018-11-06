<?php

namespace NovaCustomViews\DashboardView;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class DashboardViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::provideToScript(['novaCustomDashboard' => 'dashboard-view']);
            Nova::script('dashboard-view', __DIR__.'/../dist/js/views.js');
            Nova::style('dashboard-view', __DIR__.'/../dist/css/views.css');
        });
    }
}
