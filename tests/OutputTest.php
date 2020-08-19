<?php

namespace Rawilk\Breadcrumbs\Tests;

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Spatie\Snapshots\MatchesSnapshots;

class OutputTest extends TestCase
{
    use MatchesSnapshots;

    protected object $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Home (normal link)
        Breadcrumbs::for(
            'home',
            fn (Generator $trail) => $trail->push('Home', url('/'))
        );

        // Home > Blog (not a link)
        Breadcrumbs::for(
            'blog',
            fn (Generator $trail) => $trail->parent('home')->push('Blog')
        );

        // Home > Blog > [Category] (Active page)
        Breadcrumbs::for(
            'category',
            fn (Generator $trail, $category) => $trail->parent('blog')->push($category->title, url('blog/category/' . $category->id))
        );

        $this->category = (object) [
            'id' => 123,
            'title' => 'Example Category',
        ];
    }

    /** @test */
    public function it_renders_breadcrumbs_with_params(): void
    {
        // {{ Breadcrumbs::render('category', $category) }}
        $html = view('category')->with('category', $this->category)->render();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_renders_breadcrumbs_into_yielded_sections(): void
    {
        // @section('breadcrumbs', Breadcrumbs::render('category', $category))
        $html = view('view-section')->with('category', $this->category)->render();

        $this->assertMatchesHtmlSnapshot($html);
    }

    /** @test */
    public function it_renders_in_plain_php_views(): void
    {
        // <?php echo Breadcrumbs::render('category', $category);
        $html = view('view-php')->with('category', $this->category)->render();

        $this->assertMatchesHtmlSnapshot($html);
    }
}
