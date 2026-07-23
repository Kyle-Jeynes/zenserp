<?php

use Illuminate\Support\Facades\Route;
use Zenserp\Http\Controllers\ZenserpController;

Route::group(['prefix' => '/zenserp', 'as' => 'zenserp.', 'middleware' => ['throttle:zenserp']], function () {
    Route::get('/search', [ZenserpController::class, 'search'])->name('search');
});
