<?php

namespace App\Http\Controllers\Payment;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Omnipay\Omnipay;

class Yandex
{
    public function gateway_purchase(Request $request, Payment $payment)
    {
        $yandex = Omnipay::create('YandexKassa');

        $yandex->initialize([
            'shopId' => config('services.yandex.shop_id'),
            'secret' => config('services.yandex.secret_key'),
        ]);

        try {

            // Send purchase request
            $response = $yandex->purchase([
                'transactionId' => $payment->id,
                'amount'        => $payment->total,
                'currency'      => 'RUB',
                'description'   => $payment->package->title,
                'cancelUrl'     => route('gateway.cancel', $payment),
                'returnUrl'     => route('gateway.return', $payment),
                'notifyUrl'     => route('gateway.notify', $payment),
                'capture'       => false,
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
        $yandex = Omnipay::create('YandexKassa');

        $yandex->initialize([
            'shopId' => config('services.yandex.shop_id'),
            'secret' => config('services.yandex.secret_key'),
        ]);

        try {

            // Complete purchase
            $response = $yandex->capture([
                'transactionId'        => $payment->id,
                'transactionReference' => $payment->reference,
                'amount'               => $payment->total,
                'currency'             => 'RUB',
                'description'          => $payment->package->title,
                'cancelUrl'            => route('gateway.cancel', $payment),
                'returnUrl'            => route('gateway.return', $payment),
                'notifyUrl'            => route('gateway.notify', $payment),
            ])->send();

            // Process response
            if ($response->isSuccessful()) {

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
