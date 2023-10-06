<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('test', 'Client\TestController@test');
// Route::get('paypal', 'Client\TestController@paypalForm');
// Route::get('stripe', 'Client\TestController@stripeForm');
// Route::post('stripe/charge', 'Client\TestController@stripeCharge');



Route::prefix('webhooks')->group(function(){
    Route::post('stripe', 'Client\PaymentController@stripeWebhook');
    Route::post('paypal', 'Client\PaymentController@paypalWebhook');
});
Route::get('/', function () {
    return view('welcome');
});
