<?php

namespace AhsanDev\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, string|array $default = null)
 * @method static bool put(string|array $key, string|array $value = null)
 *
 * @see \AhsanDev\Support\Option
 */
class Option extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'option';
    }
}
