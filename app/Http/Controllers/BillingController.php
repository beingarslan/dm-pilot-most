<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\Instamojo as InstamojoPayment;
use App\Http\Controllers\Payment\PayPal as PayPalPayment;
use App\Http\Controllers\Payment\Paystack as PaystackPayment;
use App\Http\Controllers\Payment\Stripe as StripePayment;
use App\Http\Controllers\Payment\Tinkoff as TinkoffPayment;
use App\Http\Controllers\Payment\Yandex as YandexPayment;
use App\Models\Package;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $user                    = $request->user();
        $packages                = Package::visible()->get();
        $subscribed              = $user->subscribed();
        $on_trial                = $user->onTrial();
        $currency_code           = config('pilot.CURRENCY_CODE');
        $currency_symbol         = config('pilot.CURRENCY_SYMBOL');
        $subscription_title      = null;
        $subscription_expires_in = 0;

        if ($subscribed) {
            $subscription_title      = $user->package->title;
            $subscription_expires_in = $user->package_ends_at->diffInDays();
        }

        if ($on_trial) {
            $subscription_expires_in = $user->trial_ends_at->diffInDays();
        }

        return view('billing.index', compact(
            'packages',
            'subscribed',
            'on_trial',
            'currency_code',
            'currency_symbol',
            'subscription_title',
            'subscription_expires_in'
        ));
    }

    public function package(Request $request, Package $package)
    {
        // Free package
        if ($package->price_in_cents == 0) {

            // Create a payment
            $payment = Payment::create([
                'user_id'    => $request->user()->id,
                'package_id' => $package->id,
                'gateway'    => 'free',
                'total'      => $package->price,
                'is_paid'    => true,
                'currency'   => config('pilot.CURRENCY_CODE'),
            ]);

            // Update subscription
            $payment->applyPayment();

            return redirect()->route('billing.index')
                ->with('success', __('Thank you! Your <strong>:plan</strong> plan is activated successfully.', [
                    'plan' => $package->title,
                ]));
        }

        $currency_code   = config('pilot.CURRENCY_CODE');
        $currency_symbol = config('pilot.CURRENCY_SYMBOL');

        return view('billing.package', compact(
            'package',
            'currency_code',
            'currency_symbol'
        ));
    }

    public function gateway_purchase(Request $request, Package $package, $gateway)
    {
        // Create a payment
        $payment = Payment::create([
            'user_id'    => $request->user()->id,
            'package_id' => $package->id,
            'gateway'    => $gateway,
            'total'      => $package->price,
            'is_paid'    => false,
            'currency'   => config('pilot.CURRENCY_CODE'),
        ]);

        switch ($gateway) {

            case 'stripe':

                return (new StripePayment)->gateway_purchase($request, $payment);

                break;

            case 'paypal':

                return (new PayPalPayment)->gateway_purchase($request, $payment);

                break;

            case 'yandex':

                return (new YandexPayment)->gateway_purchase($request, $payment);

                break;

            case 'instamojo':

                return (new InstamojoPayment)->gateway_purchase($request, $payment);

                break;

            case 'tinkoff':

                return (new TinkoffPayment)->gateway_purchase($request, $payment);

                break;

            case 'paystack':

                return (new PaystackPayment)->gateway_purchase($request, $payment);

                break;

            default:

                return redirect()->route('billing.package', $package)
                    ->with('error', __('Unsupported payment gateway'));

                break;
        }
    }

    public function gateway_return(Request $request, Payment $payment)
    {
        switch ($payment->gateway) {

            case 'stripe':

                return (new StripePayment)->gateway_return($request, $payment);

                break;

            case 'paypal':

                return (new PayPalPayment)->gateway_return($request, $payment);

                break;

            case 'yandex':

                return (new YandexPayment)->gateway_return($request, $payment);

                break;

            case 'instamojo':

                return (new InstamojoPayment)->gateway_return($request, $payment);

                break;

            case 'tinkoff':

                return (new TinkoffPayment)->gateway_return($request, $payment);

                break;

            case 'paystack':

                return (new PaystackPayment)->gateway_return($request, $payment);

                break;

            default:

                return redirect()->route('billing.package', $payment->package)
                    ->with('error', __('Unsupported payment gateway'));

                break;
        }

    }

    public function gateway_cancel(Request $request, Payment $payment)
    {
        return redirect()->route('billing.index')
            ->with('error', __('You have cancelled your recent payment.'));

    }

    public function gateway_notify(Request $request, $gateway)
    {
        switch ($gateway) {

            case 'stripe':

                return (new StripePayment)->gateway_notify($request);

                break;

            case 'paypal':

                return (new PayPalPayment)->gateway_notify($request);

                break;

            case 'yandex':

                return (new YandexPayment)->gateway_notify($request);

                break;

            case 'instamojo':

                return (new InstamojoPayment)->gateway_notify($request);

                break;

            case 'tinkoff':

                return (new TinkoffPayment)->gateway_notify($request);

                break;

            case 'paystack':

                return (new PaystackPayment)->gateway_notify($request);

                break;

            default:

                return redirect()->route('billing.index')
                    ->with('error', __('Unsupported payment gateway'));

                break;
        }
    }

    public function cancel(Request $request)
    {
        $user = $request->user();

        $user->update([
            'package_id'      => null,
            'package_ends_at' => null,
            'trial_ends_at'   => null,
        ]);

        // Cancel all subscriptions
        $user->payments()->paid()->each(function ($payment) use ($request) {

            switch ($payment->gateway) {

                case 'stripe':

                    (new StripePayment)->gateway_cancel($request, $payment);

                    break;

                case 'paypal':

                    (new PayPalPayment)->gateway_cancel($request, $payment);

                    break;

                case 'yandex':

                    (new YandexPayment)->gateway_cancel($request, $payment);

                    break;

                case 'instamojo':

                    (new InstamojoPayment)->gateway_cancel($request, $payment);

                    break;

                case 'tinkoff':

                    (new TinkoffPayment)->gateway_cancel($request, $payment);

                    break;

                case 'paystack':

                    (new PaystackPayment)->gateway_cancel($request, $payment);

                    break;
            }

        });

        return redirect()->route('billing.index')
            ->with('success', __('You have cancelled your subscription.'));
    }

    public function payments(Request $request, Payment $payment)
    {
        $stats = DB::table('payments')
            ->selectRaw('count(id) AS total, is_paid')
            ->whereRaw('created_at BETWEEN CURDATE() - INTERVAL 120 DAY AND CURDATE()')
            ->where(function ($q) use ($request) {
                if ($request->filled('is_paid')) {
                    $q->where('is_paid', $request->is_paid);
                }
            })
            ->groupBy(DB::Raw('DAY(created_at), is_paid'))
            ->orderBy(DB::Raw('DAY(created_at)'))
            ->get();

        $chart['paid']     = $stats->where('is_paid', '1')->pluck('total')->join(',');
        $chart['not_paid'] = $stats->where('is_paid', '0')->pluck('total')->join(',');

        $data = Payment::with([
            'user',
            'package',
        ])
            ->where(function ($q) use ($request) {
                if ($request->filled('is_paid')) {
                    $q->where('is_paid', $request->is_paid);
                }
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('billing.payments', compact(
            'data',
            'chart'
        ));
    }
}
