<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Proxy;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Console\Command;

class ValidateProxies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:proxy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check proxy validity';

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
        Proxy::each(function ($proxy) {

            $this->info('Checking proxy: ' . $proxy->server);

            // Check for proxy validity
            try {

                $client = new GuzzleClient();

                $client->request('GET', 'https://www.instagram.com', [
                    'exceptions'     => false,
                    'idn_conversion' => false,
                    'proxy'          => $proxy->server,
                    'verify'         => true,
                    'timeout'        => 10,
                ]);

                $this->info('Proxy is valid');

                $proxy->is_active = true;

            } catch (\Exception $e) {

                $this->error('Proxy is not valid');

                Account::where('proxy_id', $proxy->id)->withoutGlobalScopes()->update([
                    'proxy_id' => null,
                ]);

                $proxy->is_active = false;
            }

            sleep(10);

            $proxy->save();
        });

    }
}
