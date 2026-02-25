<?php

declare(strict_types=1);

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', static fn () => \view('app'))->middleware(['verify.shopify'])->name('home');
Route::view('login', 'auth.login')->name('login');
Route::post('login', [AppController::class, 'login'])->name('login.post');

/* Update or reauthorize user */
Route::get('/update-app', [\App\Http\Controllers\AppController::class, 'updateApp']);

// Fallback Route
Route::get('/{path?}', static fn () => \view('app'))->middleware(['verify.shopify'])->name('home.fallback');
