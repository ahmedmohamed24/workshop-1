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

    Route::post('/card', [\App\Http\Controllers\CardController::class, 'save'])->name('card.save');
    Route::put('/{boardId}/{cardId}', [\App\Http\Controllers\CardController::class, 'update'])->name('card.update');
    Route::delete('/{boardId}/{cardId}', [\App\Http\Controllers\CardController::class, 'delete'])->name('card.delete');

    Route::post('/{boardId}/{cardId}/item', [\App\Http\Controllers\ItemController::class, 'save'])->name('item.save');
    Route::put('/{boardId}/{cardId}/{itemId}', [\App\Http\Controllers\ItemController::class, 'update'])->name('item.update');
    Route::delete('/{boardId}/{cardId}/{itemId}', [\App\Http\Controllers\ItemController::class, 'delete'])->name('item.delete');
});
