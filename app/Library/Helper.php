<?php

namespace App\Library;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Cache;

class Helper
{
    public static function bytes_to_human($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function sec_to_hms($seconds)
    {
        if ($seconds) {
            $hours = floor($seconds / 3600);
            $mins  = floor($seconds / 60 % 60);
            $secs  = floor($seconds % 60);

            return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        }
        return '00:00:00';
    }

    public static function calc_bar($completed, $max)
    {
        $max       = ($max <= 0 ? 1 : $max);
        $completed = ($completed > $max ? $max : $completed);

        return round($completed * 100 / $max);
    }

    public static function setEnv($data)
    {
        if (empty($data) || !is_array($data) || !is_file(base_path('.env'))) {
            return false;
        }

        $env = file_get_contents(base_path('.env'));

        $env = explode("\n", $env);

        foreach ($data as $data_key => $data_value) {

            $updated = false;

            foreach ($env as $env_key => $env_value) {

                $entry = explode('=', $env_value, 2);

                // Check if new or old key
                if ($entry[0] == $data_key) {
                    $env[$env_key] = $data_key . '=' . $data_value;
                    $updated       = true;
                } else {
                    $env[$env_key] = $env_value;
                }
            }

            // Lets create if not available
            if (!$updated) {
                $env[] = $data_key . '=' . $data_value;
            }
        }

        $env = implode("\n", $env);

        file_put_contents(base_path('.env'), $env);

        return true;
    }

    public static function getFreeProxy($cacheLifetime = 3600)
    {
        if (Cache::has('freePublicProxy')) {
            return Cache::get('freePublicProxy');
        }

        try {

            // Get random free proxy
            $client   = new GuzzleClient();
            $response = $client->request('GET', 'https://api.getproxylist.com/proxy', [
                'idn_conversion' => false,
                'verify'         => false,
                'http_errors'    => false,
                'verify'         => false,
                'timeout'        => 10,
            ]);

            $responseContents = $response->getBody()->getContents();
            $responseJSON     = json_decode($responseContents, true);

            if ($response->getStatusCode() == 200) {

                $proxy = $responseJSON['protocol'] . '://' . $responseJSON['ip'] . ':' . $responseJSON['port'];

                // Test proxy
                $testClient   = new GuzzleClient();
                $testResponse = $testClient->request('GET', 'https://www.instagram.com', [
                    'idn_conversion' => false,
                    'exceptions'     => false,
                    'proxy'          => $proxy,
                    'verify'         => false,
                    'timeout'        => 10,
                ]);

                Cache::put('freePublicProxy', $proxy, $cacheLifetime);

                return $proxy;
            }

        } catch (\Exception $e) {

        }

        return false;

    }

    public static function wildcardSearch($wildcard_pattern, $keyword)
    {
        foreach ($wildcard_pattern as $key => $value) {

            $regex = str_replace(
                array("\*", "\?"), // wildcard chars
                array('.*', '.'), // regexp chars
                preg_quote($value)
            );

            if (preg_match('/^' . $regex . '$/is', $keyword, $result)) {
                return $result;
            }
        }

        return false;
    }

}
