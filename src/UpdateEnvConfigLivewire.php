<?php

namespace AhsanDev\Support;

use Illuminate\Support\Str;

class UpdateEnvConfigLivewire
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $configs;

    /**
     * Create a new instance.
     *
     * @param  array  $attributes
     * @param  array  $configs
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function __construct(array $attributes, array $configs)
    {
        $this->configs = $configs;
        $this->attributes = $attributes;

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
                    $config.'='.$this->prepareValue($this->attributes[$value]),
                    file_get_contents($path)
                )
            );
        }
    }

    /**
     * Handle Env Config.
     *
     * @return void
     */
    public function prepareValue($value)
    {
        if (Str::contains($value, ' ')) {
            return "'${value}'";
        }

        return $value;
    }
}
