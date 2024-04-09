<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::get('/test/public', function () {
    return scandir(public_path());
});
Route::get('/test/build', function () {
    return scandir(public_path('build'));
});
Route::get('/test/assets', function () {
    return scandir(public_path('build/assets'));
});
