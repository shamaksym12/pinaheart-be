<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Client\PaymentService;
use App\Services\Client\WebhookService;
use App\Plan;
use App\PaypalSubscription;
use App\StripeSubscription;
use App\Http\Requests\Client\Payment\StripeCreate as PaymentStripeCreateRequest;
use App\Http\Requests\Client\Payment\PaypallCreate as PaymentPaypallCreateRequest;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(
        PaymentService $paymentService,
        WebhookService $webhookService
        )
    {
        $this->paymentService = $paymentService;
        $this->webhookService = $webhookService;
    }

    public function getPlans(Request $request)
    {
        return $this->paymentService->getPlans($request);
    }

    public function createStripePayment(Plan $plan, PaymentStripeCreateRequest $request)
    {
        return $this->paymentService->createStripePayment($plan, $request);
    }

    public function createPaypallPayment(Plan $plan, PaymentPaypallCreateRequest $request)
    {
        return $this->paymentService->createPaypallPayment($plan, $request);
    }

    public function getSubscriptions(Request $request)
    {
        return $this->paymentService->getSubscriptions($request);
    }

    public function stopPaypalSubscription(PaypalSubscription $paypalSubscription, Request $request)
    {
        return $this->paymentService->stopPaypalSubscription($paypalSubscription, $request);
    }

    public function startPaypalSubscription(PaypalSubscription $paypalSubscription, Request $request)
    {
        return $this->paymentService->startPaypalSubscription($paypalSubscription, $request);
    }

    public function stopStripeSubscription(StripeSubscription $stripeSubscription, Request $request)
    {
        return $this->paymentService->stopStripeSubscription($stripeSubscription, $request);
    }

    public function startStripeSubscription(StripeSubscription $stripeSubscription, Request $request)
    {
        return $this->paymentService->startStripeSubscription($stripeSubscription, $request);
    }

    public function cancelPaypalSubscription(PaypalSubscription $paypalSubscription, Request $request)
    {
        return $this->paymentService->cancelPaypalSubscription($paypalSubscription, $request);
    }

    public function cancelStripeSubscription(StripeSubscription $stripeSubscription, Request $request)
    {
        return $this->paymentService->cancelStripeSubscription($stripeSubscription, $request);
    }

    public function stripeWebhook(Request $request)
    {
        return $this->webhookService->stripeWebhook($request);
    }

    public function paypalWebhook(Request $request)
    {
        return $this->webhookService->paypalWebhook($request);
    }

    public function checkEndPeriod(Request $request)
    {
        return $this->paymentService->checkEndPeriod($request);
    }

    public function checkExpiredSubscription(Request $request)
    {
        return $this->paymentService->checkExpiredSubscription($request);
    }
}
