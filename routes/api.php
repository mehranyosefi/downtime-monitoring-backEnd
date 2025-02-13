<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\HandleLocale;
use App\Http\Middleware\HandleSetConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');


// Route::group(['namespace' => 'Index'] . function () {
// });
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::apiResource('/user', UserController::class);


Route::group(
    array(
        'namespace' => 'App\Http\Controllers\Pages',
        'middleware' => [HandleLocale::class, HandleSetConnection::class]
    ),
    function () {
        // Route::get('/',[
        //     'as' => 'home',
        //     'uses' => 'HomeController@index'
        //   ]);

    }
);
Route::group(array('middleware' => [HandleLocale::class]), function () {
    Route::group(array('namespace' => 'App\Http\Controllers\Pages'), function () {
        Route::get('/', 'HomeController@index')->name('home');
    });
    Route::post('/register', [AuthenticationController::class, 'register'])->name('register');
    Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
});
Route::get('/locales/{id}', [LocaleController::class, 'index'])->name('locales');
