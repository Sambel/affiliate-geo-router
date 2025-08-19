<?php

use App\Http\Controllers\RedirectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/{operatorSlug}', [RedirectionController::class, 'redirect'])
    ->where('operatorSlug', '[a-z0-9\-]+')
    ->middleware(\App\Http\Middleware\ThrottleRedirects::class)
    ->name('redirect');
