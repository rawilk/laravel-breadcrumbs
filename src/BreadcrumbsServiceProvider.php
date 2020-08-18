<?php

namespace Rawilk\Breadcrumbs;

use Illuminate\Support\ServiceProvider;
use Rawilk\Skeleton\Commands\SkeletonCommand;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/breadcrumbs.php' => config_path('breadcrumbs.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/breadcrumbs'),
            ], 'views');
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'breadcrumbs');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/skeleton.php', 'breadrumbs');
    }
}
