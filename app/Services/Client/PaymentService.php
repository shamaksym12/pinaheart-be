<?php

namespace App\Services\Client;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\CoreService;
use App\Repositories\UserRepository;
use App\Repositories\PaymentRepository;
use App\Facades\Paypal;
use App\Plan;
use App\PaypalWebhook;
use App\PaypalSubscription;
use App\StripeSubscription;
use App\Http\Resources\Client\Payment\PlanCollection as PaymentPlanCollection;
use App\Http\Resources\Client\Payment\Subscription as PaymentSubscriptionResource;
use App\Events\User\BecomePaid as UserBecomePaidEvent;
use App\Events\User\BecomeFree as UserBecomeFreeEvent;

class PaymentService extends CoreService
{
    protected $paymentRepository;
    protected $userRepository;

    public function __construct(
        PaymentRepository $paymentRepository,
        UserRepository $userRepository
    )
    {
        $this->paymentRepository = $paymentRepository;
        $this->userRepository = $userRepository;
    }

    /**Start Http */
    public function getPlans(Request $request)
    {
        $me = auth()->user();
        $items = Plan::get();
        $paypalClientId = config('services.paypal.client_id');
        $stripeApiKey = config('services.stripe.api_key');

        $result = (new PaymentPlanCollection($items))->withCustomData(compact('paypalClientId', 'stripeApiKey'));
        return response()->result($result);
    }

    public function createStripePayment(Plan $plan, Request $request)
    {
        $me = auth()->user();
        customThrowIf( ! $plan->isStripe(), 'Wrong plan');
        customThrowIf( $me->isPaid(), 'You already paid user');
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $customer = null;
        try {
            $customer = \Stripe\Customer::create([
                'email' => $me->email,
                'source' => $request->token,
            ]);
            $localData = [
                'stripe_id' => $customer->id,
                'stripe_data' => $customer,
            ];
            $this->userRepository->createStripeSubscriber($me, $localData);
        } catch (\Throwable $th) {
            customThrow($th->getMessage());
        }
        if($customer) {
            try {
                $subscription = \Stripe\Subscription::create([
                    'customer' => $customer->id,
                    'items' => [
                      [
                        'plan' => $plan->origin_id,
                      ],
                    ],
                    // 'trial_end' => now()->addSeconds(10)->timestamp,
                    'expand' => ['latest_invoice.payment_intent'],
                  ]);
                // \Log::info((array)$subscription);
                if($subscription->status == 'active') {
                    $me->update(['subscribe' => Plan::TYPE_STRIPE, 'is_paid' => true]);
                    event(new UserBecomePaidEvent($me));
                }
                $localData = [
                    'plan_id' => $plan->id,
                    'stripe_id' => $subscription->id,
                    'status' => $subscription->status,
                    'cancel_at_period_end' => $subscription->cancel_at_period_end,
                    'period_start' => Carbon::createFromTimestamp($subscription->current_period_start),
                    'period_end' => Carbon::createFromTimestamp($subscription->current_period_end),
                    'stripe_data' => $subscription,
                ];
                $localSub = $this->userRepository->createStripeSubscription($me, $localData);

                $latestInvoice = $subscription->latest_invoice;
                $paymentIntent = $latestInvoice->payment_intent;

                $localPaymentData = [
                    'plan_id' => $plan->id,
                    'subcription_id' => $localSub->id,
                    'stripe_invoice_id' => $latestInvoice->id,
                    'stripe_invoice_status' => $latestInvoice->status,
                    'stripe_payment_intent_id' => optional($paymentIntent)->id,
                    'stripe_payment_intent_status' => optional($paymentIntent)->status,
                    'amount' => ($amount = optional($latestInvoice)->total) ? $amount / 100 : 0,
                    'stripe_data' => $latestInvoice,
                ];
                $localPayment = $this->userRepository->createStripePayment($me, $localPaymentData);

                $subscriptionStatus = $subscription->status;
                $paymentIntentStatus = optional($paymentIntent)->status;

                return response()->result([
                    'subscription_status' => $subscriptionStatus,
                    'payment_intent_status' => $paymentIntentStatus,
                    'secret' => optional($paymentIntent)->client_secret,
                ]);
            } catch (\Throwable $th) {
                customThrow($th->getMessage());
            }
        }
    }
    public function createPaypallPayment(Plan $plan, Request $request)
    {
        $me = auth()->user();

        $subscriptionID = $request->subscriptionID;
        $orderID = $request->orderID;

        try {
            $subscription = Paypal::subscriptions()->get($subscriptionID);
            $localData = [
                'plan_id' => $plan->id,
                'paypal_id' => $subscriptionID,
                'auto_renewal' => $subscription->auto_renewal,
                'paypal_order_id' => $orderID,
                'status' => $subscription->status,
                'start_time' => toCarbon($subscription->start_time),
                'next_billing_time' => toCarbon(optional($subscription->billing_info)->next_billing_time),
            ];
            $localSub = $this->userRepository->createPaypalSubscription($me, $localData);
            if($subscription->status == 'ACTIVE') {
                $me->update(['subscribe' => Plan::TYPE_PAYPAL, 'is_paid' => true]);
                event(new UserBecomePaidEvent($me));
            }

            return response()->result(true);
        } catch (\Throwable $th) {
            customThrow($th->getMessage());
        }

    }

    public function getSubscriptions(Request $request)
    {
        $me = auth()->user()->load([
            'stripeSubscriptions' => function($q){
                $q->latest('updated_at');
                $q->withTrashed();
                $q->with(['payments' => function($q2){
                    $q2->latest('updated_at');
                }]);
            },
            'paypalSubscriptions' => function($q){
                $q->latest('updated_at');
                $q->with(['payments' => function($q2){
                    $q2->latest('updated_at');
                }]);
            }
        ]);

        $subscriptions = collect([$me->stripeSubscriptions, $me->paypalSubscriptions])->collapse();

        return response()->result(PaymentSubscriptionResource::collection($subscriptions));
    }

    public function stopPaypalSubscription(PaypalSubscription $paypalSubscription, Request $request)
    {
        $me = auth()->user();
        customThrowIf( $paypalSubscription->user_id != $me->id, 'Wrong subscription');
        customThrowIf( $paypalSubscription->status !== 'ACTIVE', 'Subscription not active');
        try {
            //suspend on paypal
            $data = [
                'reason' => 'User action'
            ];
            $subscription = Paypal::subscriptions()->postWithParams($paypalSubscription->paypal_id.'/suspend', [], $data);

            $paypalSubscription->update([
                'status' => 'SUSPENDED',
            ]);

            return response()->result(true, 'Subscription suspended.');

        } catch (\Throwable $th) {
            customThrow($th->getMessage());
        }
    }

    public function startPaypalSubscription(PaypalSubscription $paypalSubscription, Request $request)
    {
        $me = auth()->user();
        customThrowIf( $paypalSubscription->user_id != $me->id, 'Wrong subscription');
        customThrowIf( $paypalSubscription->status !== 'SUSPENDED', 'Subscription not suspended');
        try {
            $data = [
                'reason' => 'User action'
            ];
            $subscription = Paypal::subscriptions()->postWithParams($paypalSubscription->paypal_id.'/activate', [], $data);

            $paypalSubscription->update([
                'status' => 'ACTIVE',
            ]);

            return response()->result(true, 'Subscription activated.');
        } catch (\Throwable $th) {
            customThrow($th->getMessage());
        }
    }

    public function stopStripeSubscription(StripeSubscription $stripeSubscription, Request $request)
    {
        $me = auth()->user();
        customThrowIf( $stripeSubscription->user_id != $me->id, 'Wrong subscription');
        customThrowIf( $stripeSubscription->hand_status !== 'active', 'Subscription not active');
        try {
            $data = ['cancel_at_period_end' => true];
            //Update on stripe
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $apiSubscription = \Stripe\Subscription::update($stripeSubscription->stripe_id, $data);

            //Local
            $stripeSubscription->update($data);
            $to = Carbon::createFromTimestamp($apiSubscription->current_period_end);
            $me->update(['subscribe' => null,  'old_subscribe_to' => $to]);

            return response()->result(true, 'Subscription suspended.');

        } catch (\Throwable $th) {
            customThrow($th->getMessage());
        }
    }

    public function startStripeSubscription(StripeSubscription $stripeSubscription, Request $request)
    {
        $me = auth()->user();
        customThrowIf( $stripeSubscription->user_id != $me->id, 'Wrong subscription');
        customThrowIf( $stripeSubscription->hand_status !== 'suspended', 'Subscription not suspended');
        try {
            $data = ['cancel_at_period_end' => false];
            //Update on stripe
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $apiSubscription = \Stripe\Subscription::update($stripeSubscription->stripe_id, $data);
            //Local
            $stripeSubscription->update($data);
            $me->update(['subscribe' => Plan::TYPE_STRIPE,  'old_subscribe_to' => null]);

            return response()->result(true, 'Subscription activated.');

        } catch (\Throwable $th) {
            customThrow($th->getMessage());
        }
    }

    public function cancelPaypalSubscription(PaypalSubscription $paypalSubscription, Request $request)
    {
        $me = auth()->user();
        customThrowIf( $paypalSubscription->user_id != $me->id, 'Wrong subscription');
        customThrowIf( $paypalSubscription->status == 'CANCELLED', 'Subscription already canceled');
        try {
            //cancel on paypal
            $subscription = Paypal::subscriptions()->postWithParams($paypalSubscription->paypal_id.'/cancel', [], [
                'reason' => 'User action',
            ]);
            //
            $paypalSubscription->update([
                'status' => 'CANCELLED',
            ]);

            return response()->result(true, 'Subscription canceled.');

        } catch (\Throwable $th) {
            customThrow($th->getMessage());
        }
    }

    public function cancelStripeSubscription(StripeSubscription $stripeSubscription, Request $request)
    {
        $me = auth()->user();
        customThrowIf( $stripeSubscription->user_id != $me->id, 'Wrong subscription');
        customThrowIf( $stripeSubscription->status == 'canceled', 'Subscription already canceled');
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $sub = \Stripe\Subscription::retrieve($stripeSubscription->stripe_id);
            $sub->cancel();

            $stripeSubscription->update(['status' => $sub->status]);

            return response()->result(true, 'Subscription canceled.');

        } catch (\Throwable $th) {
            customThrow($th->getMessage());
        }
    }

    /**End Http */

    public function createPaypalPlans()
    {
        // Create Product
        $paypalProductData = [
            'name' => 'Subscribe',
            'description' => 'Subscribe',
            'type' => 'SERVICE',
            'category' =>  'SOFTWARE',
            'home_url'=> 'https://www.pinaheart.com'
        ];
        $product = Paypal::products()->create($paypalProductData);
        $paypalProductID = $product->id;

        //Create plans
        $plans = [
            [
                'name' => '1 month',
                'unit' => Plan::UNIT_MONTH,
                'unit_count' => 1,
                'price' => 22.99,
                'apiData' => [
                    'product_id' => $paypalProductID,
                    'name' => '1 month plan',
                    'description' => '1 month plan',
                    'billing_cycles' => [
                        [
                            'frequency' => [
                                'interval_unit' => strtoupper(Plan::UNIT_MONTH),
                                'interval_count' => 1,
                            ],
                            'sequence' => 1,
                            'tenure_type' => 'REGULAR',
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value' => 22.99,
                                    'currency_code' => 'USD'
                                ],
                            ],
                        ],
                    ],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee_failure_action' => 'CONTINUE',
                        'payment_failure_threshold' => 3,
                    ],
                ],
            ],
            [
                'name' => '3 month',
                'unit' => Plan::UNIT_MONTH,
                'unit_count' => 3,
                'price' => 59.7,
                'apiData' => [
                    'product_id' => $paypalProductID,
                    'name' => '3 month plan',
                    'description' => '3 month plan',
                    'billing_cycles' => [
                        [
                            'frequency' => [
                                'interval_unit' => strtoupper(Plan::UNIT_MONTH),
                                'interval_count' => 3,
                            ],
                            'sequence' => 1,
                            'tenure_type' => 'REGULAR',
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value' => 59.7,
                                    'currency_code' => 'USD'
                                ],
                            ],
                        ],
                    ],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee_failure_action' => 'CONTINUE',
                        'payment_failure_threshold' => 3,
                    ],
                ],
            ],
            [
                'name' => '12 month',
                'unit' => Plan::UNIT_MONTH,
                'unit_count' => 12,
                'price' => 118.8,
                'apiData' => [
                    'product_id' => $paypalProductID,
                    'name' => '12 month plan',
                    'description' => '12 month plan',
                    'billing_cycles' => [
                        [
                            'frequency' => [
                                'interval_unit' => strtoupper(Plan::UNIT_MONTH),
                                'interval_count' => 12,
                            ],
                            'sequence' => 1,
                            'tenure_type' => 'REGULAR',
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value' => 118.8,
                                    'currency_code' => 'USD'
                                ],
                            ],
                        ],
                    ],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee_failure_action' => 'CONTINUE',
                        'payment_failure_threshold' => 3,
                    ],
                ],
            ],
        ];
        foreach($plans as $item) {
            $remotePlan = Paypal::plans()->create($item['apiData']);
            $localData = [
                'type' => Plan::TYPE_PAYPAL,
                'name' => $item['name'],
                'origin_id' => $remotePlan->id,
                'unit' => $item['unit'],
                'unit_count' => $item['unit_count'],
                'price' => $item['price'],
            ];
            $localPlan = $this->paymentRepository->createPlan($localData);
        }
    }

    public function createDefaultWebhooks($url = null)
    {
        $url = $url ?? config('app.url');
        $url .= '/webhooks/paypal';
        $types = (new PaypalWebhook())->getTypesWebhooks();
        $eventTypes = [];
        foreach($types as $type) {
            $eventTypes[] = ['name' => $type];
        }
        $data = [
            'url' => $url,
            'event_types' => $eventTypes,
        ];
        Paypal::webhooks()->create($data);
    }

    public function createStripePlans()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $product = \Stripe\Product::create([
            "name" => 'Subscribe',
            "type" => "service",
        ]);

        $stripeProductID = $product->id;
        $plans = [
            [
                'name' => '1 month',
                'unit' => Plan::UNIT_MONTH,
                'unit_count' => 1,
                'price' => 22.99,
                'apiData' => [
                    "amount" => 2299,
                    "interval" => "month",
                    "interval_count" => 1,
                    "product" => $stripeProductID,
                    "currency" => "USD",
                ],
            ],
            [
                'name' => '3 month',
                'unit' => Plan::UNIT_MONTH,
                'unit_count' => 3,
                'price' => 59.7,
                'apiData' => [
                    "amount" => 5970,
                    "interval" => "month",
                    "interval_count" => 3,
                    "product" => $stripeProductID,
                    "currency" => "USD",
                    // "id" => "three-month",
                ],
            ],
            [
                'name' => '12 month',
                'unit' => Plan::UNIT_MONTH,
                'unit_count' => 12,
                'price' => 118.8,
                'apiData' => [
                    "amount" => 11880,
                    "interval" => "month",
                    "interval_count" => 12,
                    "product" => $stripeProductID,
                    "currency" => "USD",
                    // "id" => "twelve month",
                ],
            ],
        ];
        foreach($plans as $item) {
            $remotePlan = \Stripe\Plan::create($item['apiData']);
            $localData = [
                'type' => Plan::TYPE_STRIPE,
                'name' => $item['name'],
                'origin_id' => $remotePlan->id,
                'unit' => $item['unit'],
                'unit_count' => $item['unit_count'],
                'price' => $item['price'],
            ];
            $localPlan = $this->paymentRepository->createPlan($localData);
        }
    }

    public function checkEndPeriod($request) {
        $me = auth()->user()->load([
            'stripeSubscriptions' => function($q){
                $q->latest('updated_at');
                $q->withTrashed();
                $q->with(['payments' => function($q2){
                    $q2->latest('updated_at');
                }]);
            },
            'paypalSubscriptions' => function($q){
                $q->latest('updated_at');
                $q->with(['payments' => function($q2){
                    $q2->latest('updated_at');
                }]);
            }
        ]);

        $subscriptions = collect([$me->stripeSubscriptions, $me->paypalSubscriptions])->collapse();
        
        $subscriptions = $subscriptions->filter(function($item) {
            $endOfMonth = now()->endOfMonth();            
            return $item->status == "canceled" && $endOfMonth->subDay(9)->isToday();
        });

        if ( ! empty($subscriptions) && $subscriptions->count()) {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();

            $showed = $me->showedAutoRenewal()->where('created_at', '>', $startOfMonth)->where('created_at', '<', $endOfMonth)->first();       

            if ( ! $showed) {
                $me->showedAutoRenewal()->create([
                    'user_id' => $me->id,
                    'subscription_type' => $me->subscribe,
                    'subscription_id' => $subscriptions->first()->id,
                ]);
    
                return response()->result(true);
            }
        }

        return response()->result(false);
    }

    public function checkExpiredSubscription($request) {
        $me = auth()->user()->load([
            'stripeSubscriptions' => function($q){
                $q->latest('updated_at');
                $q->withTrashed();
                $q->with(['payments' => function($q2){
                    $q2->latest('updated_at');
                }]);
            },
            'paypalSubscriptions' => function($q){
                $q->latest('updated_at');
                $q->with(['payments' => function($q2){
                    $q2->latest('updated_at');
                }]);
            }
        ]);

        $subscriptions = collect([$me->stripeSubscriptions, $me->paypalSubscriptions])->collapse();
        
        $subscriptions = $subscriptions->filter(function($item) {
            $periodEndPlusDay = $item->period_end->addDay();    
            return $item->status == "canceled" && $periodEndPlusDay->isToday();
        });

        if ( ! empty($subscriptions) && $subscriptions->count()) {                        
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();

            $showed = $me->showedExpiredModal()->where('created_at', '>', $startOfMonth)->where('created_at', '<', $endOfMonth)->first();       
            
            if ( ! $showed) {
                $me->showedExpiredModal()->create([
                    'user_id' => $me->id,
                    'subscription_type' => $me->subscribe,
                    'subscription_id' => $subscriptions->first()->id,
                ]);
    
                return response()->result($subscriptions->first());
            }
        }

        return response()->result(false);
    }
}
