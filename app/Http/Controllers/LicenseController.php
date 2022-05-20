<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class LicenseController extends Controller
{
    public function check()
    {
        return view('license.license');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|size:36',
        ]);

        try {

            $client   = new GuzzleClient();
            $response = $client->request('GET', 'https://license.dmpilot.live', [
                'idn_conversion' => false,
                'verify'         => false,
                'http_errors'    => false,
                'query'          => [
                    'code'    => $request->input('code'),
                    'ip'      => $request->server('SERVER_ADDR'),
                    'url'     => config('app.url'),
                    'version' => config('pilot.version'),
                ],
            ]);

            $responseCode     = $response->getStatusCode();
            $responseContents = $response->getBody()->getContents();
            $responseJSON     = json_decode($responseContents, true);

            if ($responseCode == 200 && $responseJSON['result'] == 'success') {
                $license = base64_decode($responseJSON['license']);

                try {
                    $encrypted = Crypt::encrypt([
                        'license' => $license,
                        'code'    => $request->input('code'),
                    ]);

                    Storage::disk('local')->put('pilot.license', $encrypted);

                    return redirect()->route('settings.index')->with('success', __('Thank you! DM Pilot license is valid!'));

                } catch (\Exception $e) {
                    return redirect()->route('settings.license.check')->with('error', $responseJSON['message']);
                }
            } else {
                return redirect()->route('settings.license.check')->with('error', $responseJSON['message']);
            }

        } catch (\Exception $e) {
            return redirect()->route('settings.license.check')->with('error', $e->getMessage());
        }

    }

}
