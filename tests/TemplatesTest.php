<?php

namespace Rawilk\Breadcrumbs\Tests;

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Rawilk\Breadcrumbs\Tests\Concerns\AssertsSnapshots;

class TemplatesTest extends TestCase
{
    use AssertsSnapshots;

    protected object $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Home (normal link)
        Breadcrumbs::for('home', fn (Generator $trail) => $trail->push('Home', url('/')));

        // Home > Blog (not a link)
        Breadcrumbs::for('blog', fn (Generator $trail) => $trail->parent('home')->push('Blog'));

        // Home > Blog > [Category] (active page)
        Breadcrumbs::for('category', fn (Generator $trail, $category) => $trail->parent('blog')->push($category->title, url('blog/category/' . $category->id)));

        $this->category = (object) [
            'id' => 123,
            'title' => 'Example Category',
        ];
    }

    /**
     * @test
     * @dataProvider viewProvider
     * @param string $view
     */
    public function it_renders_each_package_template_view($view): void
    {
        $html = Breadcrumbs::view("breadcrumbs::{$view}", 'category', $this->category);

        $this->assertHtml($html);
    }

    public function viewProvider(): \Generator
    {
        foreach (glob(__DIR__ . '/../resources/views/*.blade.php') as $filename) {
            yield basename($filename, '.blade.php') => [basename($filename, '.blade.php')];
        }
    }
}
