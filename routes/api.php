<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    Route::group(['prefix' => 'auth'], function(){
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('me', 'AuthController@me');
    });

    Route::group(['prefix' => 'game'], function(){
        Route::post('new', 'GameController@create');
        Route::post('{id}/choose', 'GameController@choose');
        Route::get('/list', 'GameController@listGames');
        Route::get('/load/{id}', 'GameController@loadGame');
        Route::get('{id}/render', 'GameController@render');
    });
    
    Route::resource('user', 'UserController')->except([
        'create', 'edit', 'index'
    ]);

});

Route::get('/unlogged', function(){
    return Response::json(['error'=>'User not logged'],403);
})->name('unlogged');


