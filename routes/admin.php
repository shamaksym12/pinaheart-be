<?php

use Illuminate\Http\Request;

Route::namespace('Admin')->group(function(){
    /**Public routes */
    Route::post('login', 'AuthController@login');

    /**Private routes */
    Route::middleware('auth:api', 'can:manage-admin')->group(function(){
        Route::get('user', 'AuthController@user');

        Route::prefix('users')->group(function(){
            Route::get('','UserController@list');
            Route::get('{user}','UserController@get');
            Route::put('{user}/block','UserController@toggleUserBlock');
            Route::put('{user}/comment','UserController@setComment');

            Route::put('{user}/email','UserController@updateEmail');
            Route::put('{user}/password','UserController@updatePassword');
        });

        Route::prefix('orders')->group(function(){
            Route::get('', 'OrdersController@all');
        });

        Route::prefix('photos')->group(function(){
            Route::get('', 'UserController@listWithPhotos');
            Route::put('{photo}/approve', 'UserController@approvePhoto');
            Route::put('{photo}/disapprove', 'UserController@disapprovePhoto');
        });
        
        Route::prefix('coupons')->group(function(){
            Route::get('', 'CouponController@list');
            Route::post('', 'CouponController@create');
            Route::get('{coupon}', 'CouponController@get');
            Route::put('{coupon}/pause', 'CouponController@pauseCoupon');
            Route::put('{coupon}/unpause', 'CouponController@unpauseCoupon');
            Route::delete('{coupon}', 'CouponController@deleteCoupon');
        });

        Route::prefix('logs')->group(function() {
            Route::get('', 'ActivityLogsController@getAll');
        });
    });
});