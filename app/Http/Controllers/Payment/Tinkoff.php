<?php

namespace App\Http\Controllers\Payment;

use App\Library\TinkoffMerchantAPI;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class Tinkoff
{
    public function gateway_purchase(Request $request, Payment $payment)
    {
        // Setup payment gateway
        $tinkoffAPI = new TinkoffMerchantAPI(
            config('services.tinkoff.terminal_key'),
            config('services.tinkoff.secret_key')
        );

        $user = $payment->user;

        try {

            $tinkoffAPI->init([
                'OrderId'     => $payment->id,
                'Amount'      => $payment->total * 100,
                'Recurrent'   => 'Y',
                'CustomerKey' => $user->id,
                'DATA'        => [
                    'Email' => $user->email,
                ],
            ]);

            if ($tinkoffAPI->Success == true && $tinkoffAPI->paymentUrl) {

                // Payment was successful
                $payment->reference = $tinkoffAPI->paymentId;
                $payment->save();

                return redirect()->to($tinkoffAPI->paymentUrl);

            } else {

                // Payment failed
                return redirect()->route('billing.package', $payment->package)
                    ->with('error', $tinkoffAPI->error);

            }

        } catch (\Exception $e) {

            Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());

            return redirect()->route('billing.package', $payment->package)
                ->with('error', __('Sorry, there was an error processing your payment. Please try again later.'));
        }

    }

    public function gateway_return(Request $request, Payment $payment)
    {
        return redirect()->route('billing.index');
    }

    public function gateway_notify(Request $request)
    {
        $request->validate([
            'TerminalKey' => 'required',
            'OrderId'     => 'required',
            'Success'     => 'required',
            'Status'      => 'required',
            'PaymentId'   => 'required',
            'ErrorCode'   => 'required',
            'Amount'      => 'required',
            'CardId'      => 'required',
            'Pan'         => 'required',
            'ExpDate'     => 'required',
            'Token'       => 'required',
        ]);

        $payment = Payment::where('gateway', 'tinkoff')->find($request->OrderId);

        if ($payment) {

            if ($request->Success == true && $request->Status == 'CONFIRMED') {

                // Validate signature
                $args = $request->except([
                    'Receipt',
                    'DATA',
                    'Token',
                ]);

                // Force to use string value
                $args['Success'] = 'true';

                if ($this->_genToken($args) == $request->Token) {

                    $payment->is_paid = true;

                    // Update subscription
                    $payment->applyPayment();
                }

                $payment->options = $request->all();
                $payment->save();

            }
        }

        return response('OK', 200)
            ->header('Content-Type', 'text/plain');

    }

    public function gateway_cancel(Request $request, Payment $payment)
    {

    }

    public function gateway_recurring_charge(Payment $__payment)
    {
        // Setup payment gateway
        $tinkoffAPI = new TinkoffMerchantAPI(
            config('services.tinkoff.terminal_key'),
            config('services.tinkoff.secret_key')
        );

        $payment            = $__payment->replicate();
        $payment->is_paid   = false;
        $payment->reference = null;
        $payment->save();

        try {

            $user = $payment->user;

            // Create new order
            $tinkoffAPI->init([
                'OrderId'     => $payment->id,
                'Amount'      => $payment->total * 100,
                'CustomerKey' => $user->id,
                'DATA'        => [
                    'Email' => $user->email,
                ],
            ]);

            $payment->reference = $tinkoffAPI->paymentId;
            $payment->save();

            if ($tinkoffAPI->Success == true) {

                // Charge new order
                $tinkoffAPI->Charge([
                    'PaymentId' => $payment->reference,
                    'RebillId'  => $payment->options['RebillId'],
                ]);

            }

        } catch (\Exception $e) {

            Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());

        }

    }

    private function _genToken($args)
    {
        $token            = '';
        $args['Password'] = config('services.tinkoff.secret_key');
        ksort($args);

        foreach ($args as $arg) {
            if (!is_array($arg)) {
                $token .= $arg;
            }
        }
        $token = hash('sha256', $token);

        return $token;
    }
}
