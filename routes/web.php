<?php

use Illuminate\Support\Facades\Auth;
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

// 
// 
// define pagination constants to easily change them
// 
// 

define('categoriesNumberPerPage', 5);
define('tagsNumberPerPage', 5);



Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/publish', [App\Http\Controllers\PostController::class, 'publish'])->name('publish')->middleware(['auth']);
Route::post('/store', [App\Http\Controllers\PostController::class, 'store']);
Route::post('/store-tag', [App\Http\Controllers\PostController::class, 'storeNewTag']);
Route::get('/autocomplete-tags', [App\Http\Controllers\PostController::class, 'autoCompleteTags']);
Route::get('/autocomplete-categories', [App\Http\Controllers\PostController::class, 'autoCompleteCategories']);
Route::get('/post/{slug}', [App\Http\Controllers\PostController::class, 'showPost'])->middleware('auth');
Route::get('/editPost/{id}', [App\Http\Controllers\PostController::class, 'edit'])->middleware('auth');
Route::delete('/post/{id}', [App\Http\Controllers\PostController::class, 'destroy'])->middleware('auth');


