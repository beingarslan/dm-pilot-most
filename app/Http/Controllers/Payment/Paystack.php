<?php

namespace App\Http\Controllers\Payment;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yabacon\Paystack as PaystackGateway;
use Yabacon\Paystack\Event as PaystackEvent;
use Yabacon\Paystack\Exception\ApiException as PaystackApiException;

class Paystack
{
    private $interval = [
        'day'   => 'daily',
        'week'  => 'weekly',
        'month' => 'monthly',
        'year'  => 'annually',
    ];

    public function gateway_purchase(Request $request, Payment $payment)
    {

        // Set API key
        $paystack = new PaystackGateway(config('services.paystack.secret'));

        try
        {
            // Create a plan
            $plan = $paystack->plan->create([
                'name'        => $payment->package->title,
                'description' => __('Subscription for Package: ') . $payment->package->title,
                'amount'      => $payment->package->price_in_cents,
                'interval'    => $this->interval[$payment->package->interval],
                'currency'    => $payment->currency,
            ]);

            $transaction = $paystack->transaction->initialize([
                'plan'         => $plan->data->plan_code,
                'email'        => $payment->user->email,
                'reference'    => $payment->id,
                'callback_url' => route('gateway.return', $payment),
            ]);

            // Store reference
            $payment->reference = $transaction->data->access_code;
            $payment->save();

            // Redirect to page so User can pay
            return redirect()->to($transaction->data->authorization_url);

        } catch (PaystackApiException $e) {

            Log::error('Something went wrong: ' . $e->getMessage() . ' Response: ' . json_encode($e->getResponseObject()));

            return redirect()->route('billing.package', $payment->package)
                ->with('error', __('Sorry, there was an error processing your payment. Please try again later.'));

        }

    }

    public function gateway_return(Request $request, Payment $payment)
    {
        // Set API key
        $paystack = new PaystackGateway(config('services.paystack.secret'));

        try {

            $transaction = $paystack->transaction->verify([
                'reference' => $payment->id,
            ]);

            if ($transaction->data->status === 'success') {

                return redirect()->route('billing.index')
                    ->with('success', __('Thank you for your payment! Your subscription is activated successfully.'));

            } else {

                return redirect()->route('billing.index')
                    ->with('success', __('Sorry, your payment is not successfull.'));

            }

        } catch (PaystackApiException $e) {

            Log::error('Something went wrong: ' . $e->getMessage() . ' Response: ' . json_encode($e->getResponseObject()));

            return redirect()->route('billing.package', $payment->package)
                ->with('error', __('Sorry, there was an error processing your payment. Please try again later.'));
        }

    }

    public function gateway_notify(Request $request)
    {
        // Set API key
        $paystack = new PaystackGateway(config('services.paystack.secret'));

        // Retrieve the request's body and parse it as JSON
        $event = PaystackEvent::capture();

        // Verify that the signature matches your key
        if (!$event->validFor(config('services.paystack.secret'))) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 400);

        }

        if ($event->obj->event == 'charge.success' && $event->obj->data->status == 'success') {

            $payment = Payment::where('gateway', 'paystack')
                ->where('id', $event->obj->data->reference)
                ->first();

            if ($payment) {

                $payment->is_paid = true;
                $payment->save();

                // Update subscription
                $payment->applyPayment();

                return response()->json([
                    'success' => true,
                    'message' => 'Subscription paid',
                ], 200);
            }

        }

    }

    // ToDo
    public function gateway_cancel(Request $request, Payment $payment)
    {
        // Set API key
        $paystack = new PaystackGateway(config('services.paystack.secret'));

        try {

            $transaction = $paystack->subscription->disable([
                'code'  => 'SUB_...', // Subscription code
                'token' => '', // Email token
            ]);

            if ($transaction->data->status === 'success') {

                return true;

            }

        } catch (PaystackApiException $e) {

            Log::error('Something went wrong: ' . $e->getMessage() . ' Response: ' . json_encode($e->getResponseObject()));

        }

        return false;
    }

    public function gateway_recurring_charge(Payment $payment)
    {

    }
}
