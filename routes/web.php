<?php

Route::get('/', function() {
    return [
        'status' => 'success',
        'message' => 'Halo dunia',
        'version' => '1.0.0',
    ];
});

Route::post('/auth/register', 'AuthController@register');
Route::post('/auth/login', 'AuthController@login');
Route::post('/auth/otp-forgot-password', 'AuthController@otpForgotPassword');
Route::post('/auth/verify-otp', 'AuthController@verifyOtp');

// banner
Route::get('/banners', 'BannerController@index');

// promosi modal
Route::get('/promosi-modals', 'PromosiModalController@index');

// layanan
Route::get('/layanan', 'LayananController@index');
Route::get('/layanan/{id}', 'LayananController@show');

Route::get('/posts', 'PostController@index');
Route::get('/posts/{id}', 'PostController@show');
Route::post('/posts', 'PostController@store');
Route::put('/posts/{id}', 'PostController@update');
Route::patch('/posts/{id}', 'PostController@update');
Route::delete('/posts/{id}', 'PostController@destroy');
