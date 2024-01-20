<?php

namespace AhsanDev\Support;

use AhsanDev\Support\Contracts\StaticData as StaticDataContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonSerializable;

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
     * @var string|null
     */
    protected ?string $defaultKey = null;

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
        $key = option(key: $this->getDefaultKey())
            ?? throw new InvalidArgumentException("Database options key '{$this->getDefaultKey()}' is missing.");

        return $this->items[$key]
            ?? throw new InvalidArgumentException("Invalid or undefined item key: '$key'. Item does not exist in the items array.");
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

    protected function getDefaultKey()
    {
        return $this->defaultKey
            ?? Str::of(class_basename(get_class($this)))
                ->snake()
                ->prepend('default_')
                ->value();
    }

    /**
     * Prepare the object for JSON serialization.
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }
}
