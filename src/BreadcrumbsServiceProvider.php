<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Rawilk\Breadcrumbs\Contracts\Generator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BreadcrumbsServiceProvider extends PackageServiceProvider implements DeferrableProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-breadcrumbs')
            ->hasConfigFile()
            ->hasViews('breadcrumbs');
    }

    public function packageRegistered(): void
    {
        $this->app->bind(Generator::class, config('breadcrumbs.generator_class'));

        $this->app->singleton(Breadcrumbs::class, config('breadcrumbs.breadcrumbs_class'));
    }

    public function packageBooted(): void
    {
        $this->bootBreadcrumbs();
        $this->bootBladeComponents();
    }

    protected function bootBladeComponents(): void
    {
        $this->callAfterResolving(BladeCompiler::class, function (BladeCompiler $blade) {
            $blade->component('breadcrumbs::components.breadcrumbs', 'breadcrumbs');
        });
    }

    protected function bootBreadcrumbs(): void
    {
        $files = config('breadcrumbs.files');

        if (! $files) {
            return;
        }

        $files = collect($files)->flatten();

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
