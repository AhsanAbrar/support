<?php

namespace AhsanDev\Support;

use AhsanDev\Support\Contracts\Option as OptionContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Option implements OptionContract
{
    /**
     * Get the value of an option by its key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever($this->getCacheKey($key), function () use ($key) {
            $option = DB::table('options')->whereKey($key)->first();

            if ($option) {
                return $this->parseValue($option->value);
            }
        });

        return $value ?? $default;
    }

    /**
     * Create or update an option's value by its key.
     */
    public function put(string|array $key, string|array|null $value = null): bool
    {
        if (is_array($key)) {
            if (!Arr::isAssoc($key)) {
                throw new InvalidArgumentException(
                    'When setting values in the option, you must pass an array of key / value pairs.'
                );
            }

            foreach ($key as $name => $val) {
                $this->persist($name, $val);
            }

            return true;
        } else {
            return $this->persist($key, $value);
        }
    }

    /**
     * Persist an option in the database.
     */
    protected function persist(string $key, null|string|array $value): bool
    {
        $isPersisted = DB::table('options')->updateOrInsert(
            compact('key'),
            ['value' => is_array($value) ? json_encode($value) : $value]
        );

        if ($isPersisted) {
            $this->forgetCache($key);
        }

        return $isPersisted;
    }

    /**
     * Get the cache key for the given option key.
     */
    protected function getCacheKey(string $key): string
    {
        return $this->getPrefix() . $key;
    }

    /**
     * Parse the option value.
     */
    protected function parseValue(string $value): mixed
    {
        return is_array($decoded = json_decode($value, true))
            ? $decoded
            : $value;
    }

    /**
     * Get the prefix to be used for subdomain-based options.
     */
    protected function getPrefix(): string
    {
        return request()->getHttpHost() . '.option.';
    }

    /**
     * Forget the cache for the given option key.
     */
    protected function forgetCache(string $key): bool
    {
        return Cache::forget($this->getCacheKey($key));
    }
}
