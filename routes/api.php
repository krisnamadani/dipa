<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NaiveBayesController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'get-status'], function () {
    Route::get('teras-rumah', [NaiveBayesController::class, 'terasRumah']);
    Route::get('ruang-tamu', [NaiveBayesController::class, 'ruangTamu']);
    Route::get('kamar-utama', [NaiveBayesController::class, 'kamarUtama']);
    Route::get('kamar-kedua', [NaiveBayesController::class, 'kamarKedua']);
    Route::get('dapur', [NaiveBayesController::class, 'dapur']);
    Route::get('toilet', [NaiveBayesController::class, 'toilet']);
});