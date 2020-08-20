<?php

namespace Rawilk\Breadcrumbs\Tests;

use Rawilk\Breadcrumbs\Facades\Breadcrumbs;
use Rawilk\Breadcrumbs\Support\Generator;
use Rawilk\Breadcrumbs\Tests\Concerns\AssertsSnapshots;

class RecursionTest extends TestCase
{
    use AssertsSnapshots;

    protected object $category1;
    protected object $category2;
    protected object $category3;

    protected function setUp(): void
    {
        parent::setUp();

        // Blog
        Breadcrumbs::for('blog', fn (Generator $trail) => $trail->push('Blog', url('/')));

        $this->category1 = (object) ['id' => 1, 'title' => 'Category 1'];
        $this->category2 = (object) ['id' => 2, 'title' => 'Category 2'];
        $this->category3 = (object) ['id' => 3, 'title' => 'Category 3'];
    }

    /** @test */
    public function parents_can_be_pushed_repeatedly(): void
    {
        Breadcrumbs::for('category', static function (Generator $trail, object $category) {
            $trail->parent('blog');

            foreach ($category->parents as $parent) {
                $trail->push($parent->title, url("category/{$parent->id}"));
            }

            $trail->push($category->title, url("category/{$category->id}"));
        });

        $this->category3->parents = [$this->category1, $this->category2];

        $html = Breadcrumbs::render('category', $this->category3);

        $this->assertHtml($html);
    }

    /** @test */
    public function a_trail_parent_can_be_called_recursively(): void
    {
        Breadcrumbs::for('category', static function (Generator $trail, object $category) {
            if ($category->parent) {
                $trail->parent('category', $category->parent);
            } else {
                $trail->parent('blog');
            }

            $trail->push($category->title, url("category/{$category->id}"));
        });

        $this->category1->parent = null;
        $this->category2->parent = $this->category1;
        $this->category3->parent = $this->category2;

        $html = Breadcrumbs::render('category', $this->category3);

        $this->assertHtml($html);
    }
}
