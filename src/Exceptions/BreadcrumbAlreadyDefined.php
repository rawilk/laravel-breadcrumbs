<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use Illuminate\Support\Str;
use Rawilk\Breadcrumbs\Concerns\GetsConfigBreadcrumbFiles;
use Rawilk\Breadcrumbs\Support\IgnitionLinks;

class BreadcrumbAlreadyDefined extends Exception implements ProvidesSolution
{
    use GetsConfigBreadcrumbFiles;

    protected string $name;

    public function __construct(string $name)
    {
        parent::__construct("There is already a breadcrumb named '{$name}' defined.");

        $this->name = $name;
    }

    public function getSolution(): Solution
    {
        $files = $this->getBreadcrumbFiles();

        $basePath = base_path() . DIRECTORY_SEPARATOR;
        foreach ($files as &$file) {
            $file = Str::replaceFirst($basePath, '', $file);
        }

        if (count($files) === 1) {
            $description = "Look in `{$files[0]}` for multiple breadcrumbs named `{$this->name}`.";
        } else {
            $description = "Look in the following files for multiple breadcrumbs named `{$this->name}`:\n\n- `" . implode("`\n- `", $files) . '`';
        }

        $links = [
            'Defining Breadcrumbs' => IgnitionLinks::DEFINING_BREADCRUMBS,
            'Laravel Breadcrumbs Documentation' => IgnitionLinks::DOCS,
        ];

        return BaseSolution::create('Remove the duplicate breadcrumb')
            ->setSolutionDescription($description)
            ->setDocumentationLinks($links);
    }
}
