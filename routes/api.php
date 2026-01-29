<?php

use Illuminate\Support\Facades\Route; 

Route::middleware('throttle:5,1')->group(function () {
    Route::post('auth/login', 'AuthController@login');
});

Route::middleware('throttle:5,1')->group(function () {
    Route::post('auth/register', 'UserController@store');
});

Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::post('auth/logout', 'AuthController@logout');
    Route::get('auth/me', 'AuthController@me');
    Route::post('auth/refresh', 'AuthController@refresh');
});

Route::middleware(['auth:api', 'throttle:60,1', 'role:admin'])->group(function () {
    Route::get('users', 'UserController@index');
    Route::delete('users/{user}', 'UserController@destroy');
});

Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::get('authors', 'AuthorController@index');
    Route::get('authors/{author}', 'AuthorController@show');
});

Route::middleware(['auth:api', 'throttle:60,1', 'role:admin'])->group(function () {
    Route::post('authors', 'AuthorController@store');
    Route::put('authors/{author}', 'AuthorController@update');
    Route::delete('authors/{author}', 'AuthorController@destroy');
});
