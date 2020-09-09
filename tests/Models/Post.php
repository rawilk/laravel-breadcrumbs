<?php

declare(strict_types=1);

namespace Rawilk\Breadcrumbs\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->title = "Post {$this->id}";
    }
}
