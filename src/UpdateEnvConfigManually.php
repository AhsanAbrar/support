<?php

namespace AhsanDev\Support;

use Illuminate\Support\Str;

class UpdateEnvConfigManually
{
    /**
     * @var array
     */
    protected $configs;

    /**
     * Create a new instance.
     *
     * @param  array  $configs
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function __construct(array $configs)
    {
        $this->configs = $configs;

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
                    $config.'='.$this->prepareValue($value),
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
        if (Str::contains($value, ' ')) {
            return "'${value}'";
        }

        return $value;
    }
}
