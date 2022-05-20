<?php

namespace App\Console\Commands;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class LicenseChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:license';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for license periodically';

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
        $exists = Storage::disk('local')->exists('pilot.license');

        if ($exists) {

            $license = Storage::disk('local')->get('pilot.license');

            try {
                $decrypted = Crypt::decrypt($license);

                $client   = new GuzzleClient();
                $response = $client->request('GET', 'https://license.dmpilot.live', [
                    'idn_conversion' => false,
                    'verify'         => false,
                    'http_errors'    => false,
                    'query'          => [
                        'code'    => $decrypted['code'],
                        'url'     => config('app.url'),
                        'version' => config('pilot.version'),
                    ],
                ]);

                if ($response->getStatusCode() == 200) {
                    $this->info('License is VALID');
                } else {
                    $this->info('License is NOT VALID');
                    Storage::disk('local')->delete('pilot.license');
                }

            } catch (\Exception $e) {
                $this->info('Something went wrong: ' . $e->getMessage());
            }
        }

    }
}
