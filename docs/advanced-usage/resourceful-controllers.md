---
title: Resourceful Controllers
sort: 4
---

Laravel automatically creates route names for resourceful controllers, e.g. `photo.index`, which you can use when
defining your breadcrumbs. For example:

<x-code lang="php">
// routes/web.php
Route::resource('photo', PhotoController::class);
</x-code>

The generated routes:

<x-code lang="bash">
$ php artisan route:list
+--------+----------+--------------------+---------------+-------------------------+------------+
| Domain | Method   | URI                | Name          | Action                  | Middleware |
+--------+----------+--------------------+---------------+-------------------------+------------+
|        | GET|HEAD | photo              | photo.index   | PhotoController@index   |            |
|        | GET|HEAD | photo/create       | photo.create  | PhotoController@create  |            |
|        | POST     | photo              | photo.store   | PhotoController@store   |            |
|        | GET|HEAD | photo/{photo}      | photo.show    | PhotoController@show    |            |
|        | GET|HEAD | photo/{photo}/edit | photo.edit    | PhotoController@edit    |            |
|        | PUT      | photo/{photo}      | photo.update  | PhotoController@update  |            |
|        | PATCH    | photo/{photo}      |               | PhotoController@update  |            |
|        | DELETE   | photo/{photo}      | photo.destroy | PhotoController@destroy |            |
+--------+----------+--------------------+---------------+-------------------------+------------+
</x-code>

Your breadcrumbs:

<x-code lang="php">
// routes/breadcrumbs.php

// Photos
Breadcrumbs::for(
    'photo.index',
    fn (Generator $trail) => $trail->parent('home')->push('Photos', route('photo.index'))
);

// Photos > Upload Photo
Breadcrumbs::for(
    'photo.create',
    fn (Generator $trail) => $trail->parent('photo.index')->push('Upload Photo', route('photo.create'))
);

// Photos > [Photo Name]
Breadcrumbs::for(
    'photo.show',
    fn (Generator $trail, $photo) => $trail->parent('photo.index')->push($photo->title, route('photo.show', $photo))
);

// Photos > [Photo Name] > Edit Photo
Breadcrumbs::for(
    'photo.edit',
    fn (Generator $trail, $photo) => $trail->parent('photo.show', $photo)->push('Edit Photo', route('photo.edit', $photo))
);
</x-code>

For more details see [Resource Controllers](https://laravel.com/docs/7.x/controllers#resource-controllers) in the Laravel documentation.
