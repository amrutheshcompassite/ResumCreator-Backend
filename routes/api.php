<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->group(['namespace' => 'App\Http\Controllers\API\v1'], function ($api) {

        //User Routes
        $api->get('/user/all', 'UserAPIController@index');
        $api->post('/user/register', 'UserAPIController@userRegister');
        $api->post('user/login', 'UserAPIController@login');

        $api->group(['middleware' => 'jwt.auth'], function ($api) {
            $api->get('/checkJWT', 'UserAPIController@checkJWT');
        });
    });
});