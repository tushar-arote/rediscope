<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'api'], function (Router $router){
    $router->get('/connections', 'RedisManagerController@connections');
    $router->get('/scan', 'RedisManagerController@scan');
    $router->get('/key', 'RedisManagerController@key');
    $router->delete('/key', 'RedisManagerController@destroy');
    $router->get('/info', 'RedisManagerController@info');
});

Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('rediscope');
