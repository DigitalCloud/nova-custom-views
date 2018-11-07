<?php

namespace devmtm\NovaCustomViews;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;

class NovaCustomViewsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-custom-views', __DIR__ . '/../dist/js/nova-custom-views.js');
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([ViewsCommand::class, DashboardViewCommand::class, Error403ViewCommand::class, Error404ViewCommand::class]);
    }
}
