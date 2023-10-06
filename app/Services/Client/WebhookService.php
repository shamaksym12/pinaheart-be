<?php

namespace App\Services\Client;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\CoreService;
use App\Repositories\UserRepository;
use App\Repositories\PaymentRepository;
use App\Facades\Paypal;
use App\Plan;
use App\PaypalPayment;
use App\PaypalSubscription;
use App\PaypalWebhook;
use App\StripePayment;
use App\StripeSubscription;
use App\StripeWebhook;
use App\Events\User\BecomePaid as UserBecomePaidEvent;
use App\Events\User\BecomeFree as UserBecomeFreeEvent;

class WebhookService extends CoreService
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


    public function stripeWebhook(Request $request)
    {
        $stripeData = $request->all();
        $allTypes = (new StripeWebhook)->getTypesWebhooks();
        if(($type = array_get($stripeData, 'type')) && in_array($type, $allTypes)) {
            StripeWebhook::create([
                'type' => $type,
                'stripe_data' =>$stripeData,
            ]);
            switch ($type) {
                case 'invoice.payment_failed':
                    $this->handleStripePaymentFailed(array_get($stripeData, 'data.object', []));
                    break;
                case 'invoice.payment_succeeded':
                    $this->handleStripePaymentSucceeded(array_get($stripeData, 'data.object', []));
                    break;
                case 'customer.subscription.updated':
                    $this->handleStripeSubscriptionUpdated(array_get($stripeData, 'data.object', []));
                    break;
                case 'customer.subscription.deleted':
                    $this->handleStripeSubscriptionDeleted(array_get($stripeData, 'data.object', []));
                    break;
                default:
                    # code...
                    break;
            }
        }

    }

    public function handleStripePaymentFailed(array $data)
    {
        $id = array_get($data, 'id');
        $existPayment = StripePayment::where('stripe_invoice_id', $id)->first();
        if($existPayment) {
            $existPayment->update([
                'status' => array_get($data, 'status'),
            ]);
        }
    }

    public function handleStripePaymentSucceeded(array $data)
    {
        $id = array_get($data, 'id');
        $existPayment = StripePayment::where('stripe_invoice_id', $id)->first();
        if($existPayment) {
            $dataPayment = [
                'stripe_invoice_status' => array_get($data, 'status'),
                'stripe_payment_intent_id' => array_get($data, 'payment_intent'),
            ];
            $existPayment->update($dataPayment);
        } else {
            $dataPayment = [
                'stripe_invoice_status' => array_get($data, 'status'),
            ];
        }
    }

    public function handleStripeSubscriptionUpdated(array $data)
    {
        $id = array_get($data, 'id');
        $existSubscribtion = StripeSubscription::where('stripe_id', $id)->first();
        if($existSubscribtion) {
            $status = array_get($data, 'status');
            $existSubscribtion->update([
                'status' => $status,
                'period_start' =>  Carbon::createFromTimestamp(array_get($data, 'current_period_start')),
                'period_end' =>  Carbon::createFromTimestamp(array_get($data, 'current_period_end')),
                'cancel_at_period_end' => array_get($data, 'cancel_at_period_end'),
                'stripe_data' => $data,
            ]);
            $user = $existSubscribtion->user;
            if($status == 'active') {
                $user->update(['subscribe' => Plan::TYPE_STRIPE,  'is_paid' => true]);
                event(new UserBecomePaidEvent($user));
            } else {
                $user->update(['subscribe' => null,  'is_paid' => false]);
                event(new UserBecomeFreeEvent($user));
            }
        }
    }

    public function handleStripeSubscriptionDeleted(array $data)
    {
        $id = array_get($data, 'id');
        $existSubscribtion = StripeSubscription::where('stripe_id', $id)->first();
        if($existSubscribtion) {
            $user = $existSubscribtion->user;
            $user->update(['subscribe' => null,  'old_subscribe_to' => $existSubscribtion->period_end]);

            $existSubscribtion->update([
                'status' => array_get($data, 'status'),
            ]);
            $existSubscribtion->delete();
        }
    }

    public function paypalWebhook(Request $request)
    {
        $apiData = $request->all();
        $types = (new PaypalWebhook())->getTypesWebhooks();
        if(($type = array_get($apiData, 'event_type')) && in_array($type, $types)) {
            PaypalWebhook::create([
                'type' => $type,
                'paypal_data' =>$apiData,
            ]);
            switch ($type) {
                case 'PAYMENT.SALE.COMPLETED':
                    $this->handlePaypalSaleComplited(array_get($apiData, 'resource', []));
                    break;
                case 'BILLING.SUBSCRIPTION.CREATED':
                    $this->handlePaypalSubscriptionCreated(array_get($apiData, 'resource', []));
                    break;
                case 'BILLING.SUBSCRIPTION.UPDATED':
                    $this->handlePaypalSubscriptionCreated(array_get($apiData, 'resource', []));
                    break;
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    $this->handlePaypalSubscriptionCanceled(array_get($apiData, 'resource', []));
                    break;
                case 'BILLING.SUBSCRIPTION.EXPIRED':
                    $this->handlePaypalSubscriptionExpired(array_get($apiData, 'resource', []));
                    break;
                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    $this->handlePaypalSubscriptionSuspended(array_get($apiData, 'resource', []));
                    break;
                case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    $this->handlePaypalSubscriptionActivated(array_get($apiData, 'resource', []));
                    break;
                case 'BILLING.SUBSCRIPTION.RENEWED':
                    $this->handlePaypalSubscriptionRenewed(array_get($apiData, 'resource', []));
                    break;
                default:
                    # code...
                    break;
            }
        }
    }

    public function handlePaypalSaleComplited(array $data)
    {
        $localData = [
            'paypal_id' => array_get($data, 'id'),
            'status' => array_get($data, 'state'),
            'amount' => array_get($data, 'amount.total'),
            'paypal_data' => $data,
        ];
        $exist = PaypalSubscription::where('paypal_id', array_get($data, 'billing_agreement_id'))->first();
        if($exist) {
            $localData['user_id'] = $exist->user_id;
            $localData['subscription_id'] = $exist->id;
            $paypallSubc = Paypal::subscriptions()->get($exist->paypal_id);
            $exist->update([
                'status' => $paypallSubc->status,
                'next_billing_time' => toCarbon(optional($paypallSubc->billing_info)->next_billing_time),
            ]);
        }
        PaypalPayment::create($localData);
    }

    public function handlePaypalSubscriptionCanceled(array $data)
    {
        $id = array_get($data, 'id');
        if($exists = PaypalSubscription::where('paypal_id', $id)->first()) {
            $next_billing_time = toCarbon(array_get($data, 'billing_info.next_billing_time'));
            $localData = [
                'status' => array_get($data, 'status'),
                'auto_renewal' => array_get($data, 'auto_renewal'),
                'next_billing_time' => $next_billing_time,
                'paypal_data' => $data,
            ];
            $exists->update($localData);
            $exists->user()->update(['subscribe' => null, 'old_subscribe_to' => $next_billing_time]);
            // event(new UserBecomeFreeEvent($exists->user));
        }

    }

    public function handlePaypalSubscriptionExpired(array $data)
    {
        $id = array_get($data, 'id');
        if($exists = PaypalSubscription::where('paypal_id', $id)->first()) {
            $localData = [
                'status' => array_get($data, 'status'),
                'auto_renewal' => array_get($data, 'auto_renewal'),
                'paypal_data' => $data,
            ];
            $exists->update($localData);
            $exists->user()->update(['subscribe' => null, 'is_paid' => false]);
            event(new UserBecomeFreeEvent($exists->user));
        }
    }

    public function handlePaypalSubscriptionSuspended(array $data)
    {
        $id = array_get($data, 'id');
        if($exists = PaypalSubscription::where('paypal_id', $id)->first()) {
            $oldNextBillingTime = $exists->next_billing_time;
            $localData = [
                'status' => array_get($data, 'status'),
                'auto_renewal' => array_get($data, 'auto_renewal'),
                'next_billing_time' => toCarbon(array_get($data, 'billing_info.next_billing_time')),
                'paypal_data' => $data,
            ];
            $exists->update($localData);
            $exists->user()->update(['subscribe' => null, 'old_subscribe_to' => $oldNextBillingTime]);
        }
    }

    public function handlePaypalSubscriptionActivated(array $data)
    {
        $id = array_get($data, 'id');
        if($exists = PaypalSubscription::where('paypal_id', $id)->first()) {
            $localData = [
                'status' => array_get($data, 'status'),
                'auto_renewal' => array_get($data, 'auto_renewal'),
                'next_billing_time' => toCarbon(array_get($data, 'billing_info.next_billing_time')),
                'paypal_data' => $data,
            ];
            $exists->update($localData);
            $exists->user()->update(['subscribe' => Plan::TYPE_PAYPAL, 'old_subscribe_to' => null]);
        }
    }

    public function handlePaypalSubscriptionCreated(array $data)
    {
        $id = array_get($data, 'id');
        if($exists = PaypalSubscription::where('paypal_id', $id)->first()) {
            $localData = [
                'status' => array_get($data, 'status'),
                'start_time' => toCarbon(array_get($data, 'start_time')),
                'auto_renewal' => array_get($data, 'auto_renewal'),
                'next_billing_time' => toCarbon(array_get($data, 'billing_info.next_billing_time')),
                'paypal_data' => $data,
            ];
            $exists->update($localData);
        }
    }

    public function handlePaypalSubscriptionRenewed(array $data)
    {
        \Log::info('handlePaypalSubscriptionRenewed');
        \Log::info($data);
        // $id = array_get($data, 'id');
        // if($exists = PaypalSubscription::where('paypal_id', $id)->first()) {
        //     $localData = [
        //         'status' => array_get($data, 'status'),
        //         'start_time' => toCarbon(array_get($data, 'start_time')),
        //         'paypal_data' => $data,
        //     ];
        //     $exists->update($localData);
        // }
    }
}
