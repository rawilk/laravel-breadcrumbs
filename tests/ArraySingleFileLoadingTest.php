<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Tests\Support\ConfiguresForSingleFile;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

uses(ConfiguresForSingleFile::class);

it('loads a single file in array form', function () {
    Route::get('/test', fn () => Breadcrumbs::render('single-file-test'));

    get('/test')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 1)
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Loaded');
                });
        });
});
