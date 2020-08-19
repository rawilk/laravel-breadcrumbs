<?php

return [
    /*
     * View name:
     *
     * Choose a view to display when Breadcrumbs::render() is called.
     * Built-in templates are:
     *
     * - 'breadcrumbs::tailwind' - TailwindCSS
     * - 'breadcrumbs::bootstrap4' - Bootstrap 4
     * - 'breadcrumbs::bulma' - Bulma
     * - 'breadcrumbs::json-ld' - JSON-LD Structured Data
     */
    'view' => 'breadcrumbs::tailwind',

    /*
     * Breadcrumb File(s):
     *
     * The file(s) where breadcrumbs are defined. e.g.
     * - base_path('routes/breadcrumbs.php')
     */
    'files' => [
        base_path('routes/breadcrumbs.php'),
    ],

    /*
     * Exceptions:
     *
     * Determine when this package throws exceptions.
     */
    'exceptions' => [
        // Thrown when rendering route-bound breadcrumbs but the current route doesn't have a name.
        'unnamed_route' => true,

        // Thrown when attempting to render breadcrumbs that have not been registered.
        'not_registered' => true,

        // Thrown when attempting to render "route-bound" breadcrumbs and the named route's breadcrumbs are not defined.
        'missing_route_bound_breadcrumb' => true,
    ],

    /*
     * The breadcrumbs class is responsible for registering your breadcrumbs.
     *
     * You are free to extend the package's class, or define your own.
     */
    'breadcrumbs_class' => \Rawilk\Breadcrumbs\Breadcrumbs::class,

    /*
     * The generator class is responsible for generating the breadcrumbs.
     *
     * You are free to extend the package's class, or define your own.
     * If you define your own, it must implement: Rawilk\Breadcrumbs\Contracts\Generator
     */
    'generator_class' => \Rawilk\Breadcrumbs\Support\Generator::class,
];
