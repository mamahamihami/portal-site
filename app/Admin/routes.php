<?php

use App\Admin\Controllers\BoardController;
use App\Admin\Controllers\LinkIconController;
use App\Admin\Controllers\LinkUrlController;
use Illuminate\Routing\Router;
use App\Admin\Controllers\UserController;
use App\Admin\Controllers\DepartmentController;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('users', UserController::class);
    $router->resource('departments', DepartmentController::class);
    $router->resource('link_icons', LinkIconController::class);
    $router->resource('link_urls' , LinkUrlController::class);
    $router->resource('boards', BoardController::class);
    $router->post('users/import', [UserController::class, 'csvImport']);

});
