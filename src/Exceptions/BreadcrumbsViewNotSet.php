<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use Rawilk\Breadcrumbs\Support\IgnitionLinks;

class BreadcrumbsViewNotSet extends Exception implements ProvidesSolution
{
    public function getSolution(): Solution
    {
        $links = [
            'Choosing A Breadcrumbs Template (view)' => IgnitionLinks::CHOOSING_A_VIEW,
            'Laravel Breadcrumbs Documentation' => IgnitionLinks::DOCS,
        ];

        return BaseSolution::create('Set a view for Laravel Breadcrumbs')
            ->setSolutionDescription("Please check `config/breadcrumbs.php` for a valid `'view'` (e.g. `breadcrumbs::tailwind`)")
            ->setDocumentationLinks($links);
    }
}
