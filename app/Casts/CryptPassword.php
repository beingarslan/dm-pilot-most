<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CryptPassword implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        if (function_exists('openssl_decrypt')) {
            return openssl_decrypt($value, "AES-128-ECB", md5(config('app.key', 'DM-Pilot')));
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if (function_exists('openssl_encrypt')) {
            return openssl_encrypt($value, "AES-128-ECB", md5(config('app.key', 'DM-Pilot')));
        }

        return $value;
    }
}
