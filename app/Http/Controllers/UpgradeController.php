<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UpgradeController extends Controller
{
    public function index(Request $request)
    {
        return view('settings.upgrade');
    }

    public function check()
    {
        $exists = Storage::disk('local')->exists('pilot.license');

        if ($exists) {

            $license = Storage::disk('local')->get('pilot.license');

            try {
                $decrypted = Crypt::decrypt($license);

                $client   = new GuzzleClient();
                $response = $client->request('GET', 'https://license.dmpilot.live/version', [
                    'idn_conversion' => false,
                    'verify'         => false,
                    'http_errors'    => false,
                    'query'          => [
                        'code'    => $decrypted['code'],
                        'url'     => config('app.url'),
                        'version' => config('pilot.version'),
                    ],
                ]);

                $response->getStatusCode();
                $responseContents = $response->getBody()->getContents();
                $responseJSON     = json_decode($responseContents, true);

                if ($response->getStatusCode() == 200) {

                    if ($responseJSON['is_upgradable'] == true) {

                        return redirect()->route('settings.upgrade.index')
                            ->with('success', __('New version :new_version is available', ['new_version' => $responseJSON['new_version']]))
                            ->with('is_upgradable', true)
                            ->with('new_version', $responseJSON['new_version']);
                    } else {

                        return redirect()->route('settings.upgrade.index')
                            ->with('success', __('Your DM Pilot version is up to date'))
                            ->with('is_upgradable', false)
                            ->with('new_version', $responseJSON['new_version']);
                    }

                } else {
                    return redirect()->route('settings.upgrade.index')->with('error', $responseJSON['message']);
                }

            } catch (\Exception $e) {
                return redirect()->route('settings.upgrade.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('settings.upgrade.index')->with('error', __('You don\'t have valid license'));
        }

    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'new_version' => 'required|regex:/^\d\.\d\.\d?$/',
        ]);

        $exists = Storage::disk('local')->exists('pilot.license');

        if ($exists) {

            $license = Storage::disk('local')->get('pilot.license');

            try {

                $archive   = storage_path('upgrade/release-' . $request->new_version . '.zip');
                $extractTo = storage_path('upgrade/release-' . $request->new_version);

                File::makeDirectory($extractTo, 0777, true, true);

                $decrypted = Crypt::decrypt($license);

                $client   = new GuzzleClient();
                $response = $client->request('GET', 'https://license.dmpilot.live/download', [
                    'sink'           => $archive,
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

                    $zip = new \ZipArchive();

                    if ($zip->open($archive) === true) {

                        if (File::isWritable($extractTo . '/')) {

                            if ($zip->extractTo($extractTo)) {

                                $zip->close();

                                File::copyDirectory($extractTo, base_path());
                                File::cleanDirectory(storage_path('upgrade'), true);
                                Artisan::call('optimize:clear');
                                Artisan::call('migrate', ['--force' => true]);

                                return redirect()->route('update.check');

                            } else {
                                return redirect()->route('settings.upgrade.index')->with('error', __('Unable to extract archive.'));
                            }

                        } else {
                            return redirect()->route('settings.upgrade.index')->with('error', __('Directory is not writable :directory', ['directory' => $extractTo]));
                        }
                    } else {
                        return redirect()->route('settings.upgrade.index')->with('error', __('Unable to open archive.'));
                    }

                } else {
                    return redirect()->route('settings.upgrade.index')->with('error', __('Unable to download zip archive.'));
                }

            } catch (\Exception $e) {
                return redirect()->route('settings.upgrade.index')->with('error', $e->getMessage());
            }

        } else {
            return redirect()->route('settings.upgrade.index')->with('error', __('You don\'t have valid license'));
        }

    }
}
