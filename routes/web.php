<?php

Route::get('/', function() {
    return [
        'status' => 'success',
        'message' => 'API is working!',
        'version' => '1.0.0',
        'endpoints' => [
            'auth' => [
                'register' => 'POST /api/auth/register',
                'login' => 'POST /api/auth/login'
            ],
            'posts' => [
                'index' => 'GET /api/posts',
                'show' => 'GET /api/posts/{id}',
                'store' => 'POST /api/posts',
                'update' => 'PUT /api/posts/{id}',
                'destroy' => 'DELETE /api/posts/{id}'
            ]
        ],
        'documentation' => 'https://github.com/your-repo/api-docs'
    ];
});

Route::post('/auth/register', 'AuthController@register');
Route::post('/auth/login', 'AuthController@login');
Route::post('/auth/otp-forgot-password', 'AuthController@otpForgotPassword');
Route::post('/auth/verify-otp', 'AuthController@verifyOtp');

Route::get('/posts', 'PostController@index');
Route::get('/posts/{id}', 'PostController@show');
Route::post('/posts', 'PostController@store');
Route::put('/posts/{id}', 'PostController@update');
Route::patch('/posts/{id}', 'PostController@update');
Route::delete('/posts/{id}', 'PostController@destroy');
?>
