<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Preferences\PreferenceController;
use App\Http\Controllers\Articles\ArticleController;
use App\Http\Controllers\Categories\CategoriesController;
use App\Http\Controllers\Sources\SourcesController;
use App\Http\Controllers\Authors\AuthorsController;
use Illuminate\Http\Request;
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
    'middleware' => 'api'
], function ($router) {
    
    /**
     * Authentication Module
     */
    Route::group(['prefix' => 'auth'], function() {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
    

    /**
     * Preference Module
     */
    Route::group(['prefix' => 'preference'], function() {
        Route::post('add', [PreferenceController::class, 'add']);
        Route::get('', [PreferenceController::class, 'retrieveAll']);
    });


    /**
     * Article Module
     */
    Route::group(['prefix' => 'article'], function() {
        Route::get('', [ArticleController::class, 'index']);
    });

    /**
     * Reference Module
     */
    Route::group(['prefix' => 'reference'], function() {
        Route::get('categories', [CategoriesController::class, 'retrieveAll']);
        Route::get('sources', [SourcesController::class, 'retrieveAll']);
        Route::get('authors', [AuthorsController::class, 'retrieveAll']);
    });

});

