<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Rawilk\Breadcrumbs\Contracts\Generator;

class BreadcrumbsServiceProvider extends ServiceProvider implements DeferrableProvider
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

        $this->registerBreadcrumbs();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/breadcrumbs.php', 'breadcrumbs');

        $this->app->bind(Generator::class, config('breadcrumbs.generator_class'));

        $this->app->singleton(Breadcrumbs::class, config('breadcrumbs.breadcrumbs_class'));
    }

    protected function registerBreadcrumbs(): void
    {
        $files = config('breadcrumbs.files');

        if (! $files) {
            return;
        }

        if (! is_array($files)) {
            $files = [$files];
        }

        $files = Arr::flatten($files);

        foreach ($files as $file) {
            if (is_file($file)) {
                require $file;
            }
        }
    }

    public function provides(): array
    {
        return [Breadcrumbs::class];
    }
}
