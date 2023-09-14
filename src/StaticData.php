<?php

namespace AhsanDev\Support;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use JsonSerializable;
use AhsanDev\Support\Contracts\StaticData as StaticDataContract;

class StaticData implements JsonSerializable, StaticDataContract
{
    /**
     * Items mapping.
     *
     * @var array<string, mixed>
     */
    protected array $items = [];

    /**
     * The default key.
     *
     * @var string
     */
    protected string $default = '';

    /**
     * Get a specific item by its key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    /**
     * Get all items.
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get the default item.
     */
    public function default(): mixed
    {
        $key = option(key: $this->default, default: $this->default);

        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        throw new InvalidArgumentException("Invalid item key: '$key'. This key does not exist in the items array.");
    }

    /**
     * Get a random item.
     */
    public function random(int $number = null, bool $preserveKeys = false): mixed
    {
        return Arr::random($this->items, $number, $preserveKeys);
    }

    /**
     * Get items as options.
     */
    public function options(): array
    {
        return collect($this->items)
                    ->map(fn ($value, $label) => compact('label', 'value'))
                    ->values()
                    ->all();
    }

    /**
     * Prepare the object for JSON serialization.
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }
}
