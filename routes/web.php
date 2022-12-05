<?php

use App\Http\Controllers\NaiveBayesController;
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

Route::get('/', [NaiveBayesController::class, 'index'])->name('index');
Route::get('/test', [NaiveBayesController::class, 'test'])->name('test');