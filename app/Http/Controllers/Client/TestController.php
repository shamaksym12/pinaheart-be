<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Facades\Paypal;
use App\Http\Controllers\Controller;
use App\Repositories\PaymentRepository;
use GuzzleHttp\Client;

class TestController extends Controller
{
    public function test()
    {
        // $httpParams = [
        //     'start_time' => now()->startOfWeek()->toISOString(),
        //     'end_time' => now()->toISOString(),
        // ];
        // dd($httpParams);
        // $trans = Paypal::subscriptions()->get('I-55SXVJB09CXC/transactions', $httpParams);
        // dd($trans);
        // $q = Paypal::subscriptions()->get('I-5R8U6X1BB61J');
        // dd($q);
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $s = \Stripe\Subscription::retrieve('sub_Fq4AJkNBJ1gbHD');
        dd($s);

    }

    public function paypalForm()
    {
        return view('paypal-form');
    }

    public function stripeForm()
    {
        return view('stripe-form');
    }
}
