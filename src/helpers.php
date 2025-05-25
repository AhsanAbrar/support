<?php

use AhsanDev\Support\Vite;

if (! function_exists('option')) {
    /**
     * Get / set the specified option value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string|null  $key
     * @param  array|string|null  $default
     * @return mixed|\AhsanDev\Support\Option
     */
    function option(string|array|null $key = null, string|array|null $default = null)
    {
        if (is_null($key)) {
            return app('option');
        }

        if (is_array($key)) {
            return app('option')->put($key);
        }

        return app('option')->get($key, $default);
    }
}

if (! function_exists('vite_tags')) {
    /**
     * Get the path to a versioned vite file.
     *
     * @param  string  $manifestDirectory
     * @param  int  $port
     * @param  string|null  $file
     * @return \Illuminate\Support\HtmlString|string
     *
     * @throws \Exception
     */
    function vite_tags($manifestDirectory = '', $port = 5173, $file = 'main.js')
    {
        return app(Vite::class)($manifestDirectory, $port, $file);
    }
}
