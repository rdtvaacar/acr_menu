<?php

namespace Acr\Menu;

use Acr\Menu\Controllers\AcrMenuController;
use Illuminate\Support\ServiceProvider;

class AcrMenuServiceProviders extends ServiceProvider
{
    public function boot()
    {
        include(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/Views', 'acr_menu');
    }

    public function register()
    {
        $this->app->bind('AcrMenu', function () {
            return new AcrMenuController();
        });
    }
}