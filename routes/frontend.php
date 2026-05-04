<?php

use Illuminate\Support\Facades\Route;

 Route::middleware(['XSS', 'IsInstalled'])->group(function () {
    Route::get('/', function () {
        return view('vue-app');
    })->where('any', '.*');

    Route::get('/about_us', function () {
        return view('vue-app');
    })->where('any', '.*');

    Route::get('/truck/service', function () {
        return view('vue-app');
    })->where('any', '.*');

    Route::get('/tracking/log', function () {
        return view('vue-app');
    })->where('any', '.*');

    Route::get('/get_qoute', function () {
        return view('vue-app');
    })->where('any', '.*');

    Route::get('/faq', function () {
        return view('vue-app');
    })->where('any', '.*');

    Route::get('/contact', function () {
        return view('vue-app');
    })->where('any', '.*');

    Route::get('/terms/conditions', function () {
        return view('vue-app');
    })->where('any', '.*');

    Route::get('/privacy/policy', function () {
        return view('vue-app');
    })->where('any', '.*');
 
    Route::get('/network/coverage', function () {
        return view('vue-app');
    })->where('any', '.*');
    
    Route::get('/track/ship', function () {
        return view('vue-app');
    })->where('any', '.*');

});