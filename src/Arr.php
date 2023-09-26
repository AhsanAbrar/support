<?php

namespace AhsanDev\Support;

class Arr
{
    /**
     * Modify an array by excluding specified keys and optionally wrapping them in a sub-array.
     *
     * @param array $data The original array.
     * @param array $keysToWrap The keys to wrap in a sub-array.
     * @param string $subArrayKey The key for the sub-array.
     * @return array The modified array.
     */
    public static function wrapInSubarray(array $data, array $keysToWrap = [], $subArrayKey = 'sub'): array
    {
        $main = collect($data)->except($keysToWrap);
        $sub = collect($data)->only($keysToWrap);

        $sub->isNotEmpty() && $main->put($subArrayKey, $sub->all());

        return $main->all();
    }

    /**
     * Modify an array by excluding specified keys and wrapping them in a 'meta' sub-array.
     *
     * @param array $data The original array.
     * @param array $keysToWrap The keys to wrap in a 'meta' sub-array.
     * @return array The modified array with 'meta' sub-array.
     */
    public static function wrapInMeta(array $data, array $keysToWrap = []): array
    {
        return self::wrapInSubarray($data, $keysToWrap, 'meta');
    }

    /**
     * Modify an array by excluding specified keys and optionally wrapping them in a sub-array.
     *
     * @param array $data The original array.
     * @param array $keysToWrap The keys to wrap in a sub-array.
     * @param string $subArrayKey The key for the sub-array.
     * @return array The modified array.
     */
    public static function wrapInSubarray2(array $data, array $keysToWrap = [], $subArrayKey = 'sub'): array
    {
        $main = array_diff_key($data, array_flip($keysToWrap));
        $subArray = array_intersect_key($data, array_flip($keysToWrap));

        if (!empty($subArray)) {
            $main[$subArrayKey] = $subArray;
        }

        return $main;
    }
}
