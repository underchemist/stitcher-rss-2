<?php declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', 'PageController@index');

$router->get('/login', 'UserController@login');
$router->post('/login', 'UserController@login');
$router->get('/logout', 'UserController@logout');

$router->group(['middleware' => 'auth:session'], function () use ($router) {
    $router->get('/shows', 'ShowController@shows');
    $router->addRoute(['GET', 'POST'], '/search', 'ShowController@search');
});

$router->group(['middleware' => 'auth:basic'], function () use ($router) {
    $router->get('/shows/{show_id}/feed', 'ShowController@feed');
});

$router->group(['middleware' => 'auth:route'], function () use ($router) {
    $router->get(
        '/shows/{show_id}/episodes/{rss_user}/{rss_pass}/{episode_id}.mp3',
        'ShowController@episode'
    );
});

$router->get('/metrics', 'MetricController@index');
