<?php

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
Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [\App\Http\Controllers\UserAuthController::class, 'register'])->name('register');
    Route::post('/register', [\App\Http\Controllers\UserAuthController::class, 'doRegister'])->name('user.doRegister');
    Route::get('/login', [\App\Http\Controllers\UserAuthController::class, 'login'])->name('login');
    Route::post('/login', [\App\Http\Controllers\UserAuthController::class, 'doLogin'])->name('user.doLogin');
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('/board', [\App\Http\Controllers\BoardController::class, 'save'])->name('board.save');
    Route::get('/{boardSlug}', [\App\Http\Controllers\BoardController::class, 'show'])->name('board.show');
    Route::put('/{boardSlug}', [\App\Http\Controllers\BoardController::class, 'update'])->name('board.update');
    Route::delete('/{boardSlug}', [\App\Http\Controllers\BoardController::class, 'delete'])->name('board.delete');
});
