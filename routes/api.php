<?php

use App\Http\Controllers\Auth\AuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix('admin')->controller(AuthController::class)->group(function () {

    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
        Route::prefix('brand')->controller(AdminBrandController::class)->group(function () {
            Route::get('get-brands', 'getAllBrand');
            Route::post('store', 'store');
            Route::post('create', 'create');
            Route::get('/{id}', 'show');
            Route::post('update', 'update');
            Route::delete('/{id}', 'delete');
        });
    });
});
