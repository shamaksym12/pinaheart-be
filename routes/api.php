<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::namespace('Client')->group(function(){
    /**Public routes */
    Route::post('login', 'AuthController@login');
    Route::post('login-by-hash', 'AuthController@loginByHash');
    Route::post('register', 'AuthController@register');
    Route::post('recovery', 'AuthController@recovery');
    Route::post('reset', 'AuthController@reset');
    Route::post('request-unblock', 'AuthController@unblock');        
    Route::prefix('socialite')->group(function(){
        Route::post('redirect', 'AuthController@redirect');
        Route::get('google', 'AuthController@google');
        Route::get('facebook', 'AuthController@facebook');
    });
    /**Private routes */
    Route::middleware('auth:api', 'can:manage-front', 'activity')->group(function(){
        Route::get('user', 'ProfileController@getUser');
        Route::prefix('my')->group(function(){
            Route::get('profile', 'ProfileController@getMyDetailProfile');
            Route::put('email', 'ProfileController@setMyEmail');
            Route::put('password', 'ProfileController@setMyPassword');
            Route::get('info', 'ProfileController@getMyInfo');
            Route::put('info', 'ProfileController@setMyInfo');
            Route::put('off', 'ProfileController@setOff');
            Route::put('on', 'ProfileController@setOn');
            Route::put('busy', 'ProfileController@toggleBusy');
            Route::put('hidden', 'ProfileController@toggleHidden');            
            Route::get('check-unfinish-register', 'ProfileController@checkUnfinishRegister');
        });
        Route::prefix('notify-settings')->group(function(){
            Route::get('', 'ProfileController@getNotifySettings');
            Route::put('', 'ProfileController@setNotifySettings');
        });
        Route::prefix('photos')->group(function(){
            Route::get('', 'ProfileController@getPhotos');
            Route::post('', 'ProfileController@addPhoto');
            Route::put('{photo}/main', 'ProfileController@setMainPhoto');
            Route::delete('{photo}', 'ProfileController@deletePhoto');
            Route::post('upload', 'ProfileController@loadFacebookPhotos');
        });
        Route::prefix('location')->group(function(){
            Route::get('countries', 'ProfileController@getСountries');
        });
        Route::prefix('profile')->group(function(){
            Route::get('', 'ProfileController@getProfile');
            Route::post('params', 'ProfileController@setProfileParams');
            Route::get('params', 'ProfileController@getAllProfileParams');
        });
        Route::prefix('match')->group(function(){
            Route::get('', 'ProfileController@getMatches');
            Route::post('', 'ProfileController@setMatches');
            Route::get('params', 'ProfileController@getAllMatchParams');
            Route::post('params', 'ProfileController@setAllMatchParams');
        });
        Route::prefix('interest')->group(function(){
            Route::get('', 'ProfileController@getInterest');
            Route::post('', 'ProfileController@setInterest');
        });
        Route::prefix('personality')->group(function(){
            Route::get('', 'ProfileController@getPersonality');
            Route::post('', 'ProfileController@setPersonality');
        });
        Route::prefix('people')->group(function(){
            Route::get('search/params', 'PersonController@getSearchParams');
            Route::post('', 'PersonController@list');
            Route::post('smartsearch', 'PersonController@smartSearch');
            Route::get('match', 'PersonController@getMatches');
            Route::get('{user}/short', 'PersonController@getShortProfile')->middleware('can:getProfile,user');
            Route::get('{user}/detail', 'PersonController@getDetailProfile')->middleware('can:getProfile,user');
            Route::put('{user}/favorite', 'PersonController@toggleFavorite')->middleware('can:getProfile,user');
            Route::put('{user}/interest', 'PersonController@toggleInterests')->middleware('can:getProfile,user');
            Route::put('{user}/block', 'PersonController@toggleBlock')->middleware('can:getProfile,user');
            Route::put('{user}/comment', 'PersonController@updateComment')->middleware('can:getProfile,user');
            Route::put('{user}/admin-block', 'PersonController@updateAdminBlock')->middleware('can:getProfile,user');            
        });
        Route::prefix('searches')->group(function(){
            Route::get('', 'PersonController@getSearchesLists');
            Route::get('{userSearch}', 'PersonController@getSearch');
            Route::delete('{userSearch}', 'PersonController@deleteSearch');
            Route::get('{userSearch}/people', 'PersonController@getSearchPeoples');
        });
        Route::prefix('activities')->group(function(){
            Route::get('inbox', 'PersonController@getInboxActivities');
            Route::get('sent', 'PersonController@getSentActivities');
        });
        Route::prefix('dialogs')->group(function(){
            Route::put('open/{user}', 'MessageController@openDialog');
            Route::get('', 'MessageController@getDialogs');
            Route::post('{dialog}', 'MessageController@createDialogMessage');
            Route::get('{dialog}', 'MessageController@getDialogMessages');
            Route::put('delete', 'MessageController@deleteDialogs');
        });
        //Payments section
        Route::get('plans', 'PaymentController@getPlans');
        Route::prefix('payments')->group(function(){
            Route::post('stripe/{plan}', 'PaymentController@createStripePayment');
            Route::post('paypal/{plan}', 'PaymentController@createPaypallPayment');
        });
        Route::prefix('subscriptions')->group(function(){
            Route::get('', 'PaymentController@getSubscriptions');
            Route::put('paypal/{paypalSubscription}/stop', 'PaymentController@stopPaypalSubscription');
            Route::put('paypal/{paypalSubscription}/start', 'PaymentController@startPaypalSubscription');
            Route::put('stripe/{stripeSubscription}/stop', 'PaymentController@stopStripeSubscription');
            Route::put('stripe/{stripeSubscription}/start', 'PaymentController@startStripeSubscription');
            Route::get('check-end-period', 'PaymentController@checkEndPeriod');
            Route::get('check-expired-subscription', 'PaymentController@checkExpiredSubscription');            
            // Route::delete('paypal/{paypalSubscription}', 'PaymentController@cancelPaypalSubscription');
            // Route::delete('stripe/{stripeSubscription}', 'PaymentController@cancelStripeSubscription');
        });
   });
});
