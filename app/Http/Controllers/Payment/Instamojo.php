<?php

namespace App\Http\Controllers\Payment;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Omnipay\Omnipay;

class Instamojo
{
    public function gateway_purchase(Request $request, Payment $payment)
    {
        // Setup payment gateway
        $instamojo = Omnipay::create('Instamojo');
        $instamojo->initialize([
            'api_key'    => config('services.instamojo.api_key'),
            'auth_token' => config('services.instamojo.auth_token'),
            'testMode'   => config('services.instamojo.test_mode'),
        ]);

        try {

            $user = $payment->user;

            // Send purchase request
            $response = $instamojo->purchase([
                'expires_at'   => now()->addMinutes(5),
                'amount'       => $payment->total,
                'email'        => $user->email,
                'buyer_name'   => $user->name,
                'purpose'      => $payment->package->title,
                'redirect_url' => route('gateway.return', $payment),
            ])->send();

            // Process response
            if ($response->isRedirect()) {

                // Save reference
                $payment->reference = $response->getTransactionReference();
                $payment->save();

                // Redirect to offsite payment gateway
                return $response->redirect();

            } elseif ($response->isSuccessful()) {

                // Payment was successful
                $payment->reference = $response->getTransactionReference();
                $payment->is_paid   = true;
                $payment->save();

                // Update subscription
                $payment->applyPayment();

                return redirect()->route('billing.index')
                    ->with('success', __('Thank you for your payment! Your subscription is activated successfully.'));

            } else {

                // Payment failed
                return redirect()->route('billing.package', $payment->package)
                    ->with('error', $response->getMessage());

            }

        } catch (\Exception $e) {

            Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());

            return redirect()->route('billing.package', $payment->package)
                ->with('error', __('Sorry, there was an error processing your payment. Please try again later.'));
        }

    }

    public function gateway_return(Request $request, Payment $payment)
    {
        $instamojo = Omnipay::create('Instamojo');

        $instamojo->initialize([
            'api_key'    => config('services.instamojo.api_key'),
            'auth_token' => config('services.instamojo.auth_token'),
            'testMode'   => config('services.instamojo.test_mode'),
        ]);

        try {

            // Complete purchase
            $response = $instamojo->fetchPaymentRequest([
                'transactionReference' => $payment->reference,
            ])->send();

            // Process response
            if ($response->isSuccessful()) {

                // Payment was successful
                $payment->is_paid = true;
                $payment->save();

                // Update subscription
                $payment->applyPayment();

                return redirect()->route('billing.index')
                    ->with('success', __('Thank you for your payment! Your subscription is activated successfully.'));

            } else {

                // Payment failed
                return redirect()->route('billing.package', $payment->package)
                    ->with('error', $response->getMessage());

            }

        } catch (\Exception $e) {

            Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());

            return redirect()->route('billing.package', $payment->package)
                ->with('error', __('Sorry, there was an error processing your payment. Please try again later.'));
        }
    }

    public function gateway_notify(Request $request)
    {

    }

    public function gateway_cancel(Request $request, Payment $payment)
    {

    }

    public function gateway_recurring_charge(Payment $payment)
    {

    }
}
