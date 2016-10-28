<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'mandra'], function () {
    Route::get('/', 'LaravelMandra\Http\Controllers\Controller@index');
});
