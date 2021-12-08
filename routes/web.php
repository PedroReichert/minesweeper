<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('login');
});

Route::post('/auth', [AuthController::class, 'loginWeb'])->name('auth');
Route::get('/list', [GameWebController::class, 'listGames'])->name('listGames');
Route::get('/play/{id}', [GameWebController::class, 'loadGame'])->name('loadGame');
Route::get('/play/{id}/choose', [GameWebController::class, 'choose']);
Route::post('new', [GameWebController::class, 'create'])->name('new');
Route::get('/newgame',  function () {return view('newGame');});
