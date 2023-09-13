<?php

namespace AhsanDev\Support\Contracts;

interface StaticData
{
    /**
     * Get a specific item by its key.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Get all items.
     */
    public function all(): array;

    /**
     * Get the default item.
     */
    public function default(): mixed;

    /**
     * Get a random item.
     */
    public function random(int $number = null, bool $preserveKeys = false): mixed;

    /**
     * Get items as options.
     */
    public function options(): array;
}
