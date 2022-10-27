<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Tests\Support\ConfiguresForGlob;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

uses(ConfiguresForGlob::class);

it('loads all files matched by glob', function () {
    Route::get('/test', fn () => Breadcrumbs::render('multiple-file-test'))->name('test');

    get('/test')
        ->assertElementExists('ol', function (AssertElement $ol) {
            $ol->contains('li', 2)
                ->contains('li.current', 1)
                ->find('li:nth-of-type(1)', function (AssertElement $li) {
                    $li->has('text', 'Parent');
                })
                ->find('li.current', function (AssertElement $li) {
                    $li->has('text', 'Loaded');
                });
        });
});
