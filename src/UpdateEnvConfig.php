<?php

namespace AhsanDev\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UpdateEnvConfig
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $configs;

    /**
     * Create a new instance.
     *
     * @param  Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function __construct(Request $request, array $configs)
    {
        $this->configs = $configs;
        $this->request = $request;

        $this->handle();
    }

    /**
     * Handle Env Config.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->configs as $config => $value) {
            $path = base_path('.env');

            file_put_contents(
                $path,
                preg_replace(
                    '/^'.$config.'=.*/m',
                    $config.'='.$this->prepareValue($this->request->$value),
                    file_get_contents($path)
                )
            );
        }
    }

    /**
     * Handle Env Config.
     *
     * @return string
     */
    public function prepareValue($value)
    {
        // Convert the input to a string to ensure compatibility
        $value = (string) $value;

        // Check if the value contains spaces, quotes, or other special characters
        if (Str::contains($value, [' ', '#', '"', "'", '\\'])) {
            // Escape special characters like double quotes and backslashes
            $escapedValue = str_replace(['"', '\\'], ['\\"', '\\\\'], $value);

            // Wrap the value in double quotes for safety
            return "\"{$escapedValue}\"";
        }

        // Return the value as-is if no special characters are found
        return $value;
    }
}
