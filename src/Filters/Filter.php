<?php

namespace AhsanDev\Support\Filters;

use AhsanDev\Support\Helper;
use JsonSerializable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Abstract class representing a filter.
 */
abstract class Filter implements JsonSerializable
{
    /**
     * The displayable name of the filter.
     */
    public string $name;

    /**
     * The attribute/column name of the field.
     */
    public string $attribute;

    /**
     * The filter's component.
     */
    public string $component = 'filter-select';

    /**
     * Indicates if the filter should be displayed horizontally.
     */
    public bool $horizontal = false;

    /**
     * Additional meta data for the element.
     */
    public array $meta = [];

    /**
     * Apply the filter to the given query.
     */
    abstract public function apply(Builder $query, $value): Builder;

    /**
     * Get the filter's available options.
     */
    abstract public function options(): array;

    /**
     * Get the component name for the filter.
     */
    public function component(): string
    {
        return $this->component;
    }

    /**
     * Get the displayable name of the filter.
     */
    public function name(): string
    {
        return $this->name ?: Helper::humanize($this);
    }

    /**
     * Get the key for the filter.
     */
    public function key(): string
    {
        return get_class($this);
    }

    /**
     * Set the default options for the filter.
     */
    public function default(): string
    {
        return '';
    }

    /**
     * Get additional meta information to merge with the element payload.
     */
    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * Set additional meta information for the element.
     */
    public function withMeta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    /**
     * Prepare the filter for JSON serialization.
     */
    public function jsonSerialize(): array
    {
        return array_merge([
            'name' => $this->name(),
            'attribute' => $this->attribute,
            'component' => $this->component(),
            'horizontal' => $this->horizontal,
            'options' => $this->formattedOptions(),
            'value' => $this->default(),
        ], $this->meta());
    }

    /**
     * Format the options for JSON serialization.
     */
    protected function formattedOptions(): array
    {
        return collect($this->options())->map(function ($value, $key) {
            return is_array($value) ? ($value + ['value' => $key]) : ['name' => $key, 'value' => $value];
        })->values()->all();
    }
}
