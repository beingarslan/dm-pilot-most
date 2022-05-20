<?php

namespace App\Console\Commands;

use App\Http\Controllers\Payment\Instamojo as InstamojoPayment;
use App\Http\Controllers\Payment\PayPal as PayPalPayment;
use App\Http\Controllers\Payment\Paystack as PaystackPayment;
use App\Http\Controllers\Payment\Stripe as StripePayment;
use App\Http\Controllers\Payment\Tinkoff as TinkoffPayment;
use App\Http\Controllers\Payment\Yandex as YandexPayment;
use App\Models\User;
use Illuminate\Console\Command;

class BillingCycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:billing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run charge command for non-automatic payment gateways';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::whereNotNull('package_ends_at')
            ->where('package_ends_at', '<=', now())
            ->get();

        foreach ($users as $user) {

            $user->payments()->paid()->each(function ($payment) use ($user) {

                $this->info('Charging user: ' . $user->name . ' for payment: ' . $payment->id);

                switch ($payment->gateway) {

                    case 'stripe':

                        (new StripePayment)->gateway_recurring_charge($payment);

                        break;

                    case 'paypal':

                        (new PayPalPayment)->gateway_recurring_charge($payment);

                        break;

                    case 'yandex':

                        (new YandexPayment)->gateway_recurring_charge($payment);

                        break;

                    case 'instamojo':

                        (new InstamojoPayment)->gateway_recurring_charge($payment);

                        break;

                    case 'tinkoff':

                        (new TinkoffPayment)->gateway_recurring_charge($payment);

                        break;

                    case 'paystack':

                        (new PaystackPayment)->gateway_recurring_charge($payment);

                        break;
                }

            });

        }
    }
}
