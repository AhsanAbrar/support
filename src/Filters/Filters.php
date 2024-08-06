<?php

namespace AhsanDev\Support\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class Filters
 *
 * Base class for applying filters to an Eloquent query builder.
 */
class Filters
{
    /**
     * The HTTP request instance.
     */
    protected Request $request;

    /**
     * The Eloquent query builder instance.
     */
    protected Builder $builder;

    /**
     * Default filters to operate upon.
     */
    protected array $filters = ['search', 'orderBy'];

    /**
     * Registered filters to operate upon.
     */
    protected array $filtersArray = [];

    /**
     * Create a new Filters instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply all relevant filters to the query builder.
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $filter) {
            $this->filtersArray[$filter->attribute] = $filter;
        }

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            } elseif (isset($this->filtersArray[$filter])) {
                $this->filtersArray[$filter]->apply($builder, $value);
            }
        }

        return $this->builder;
    }

    /**
     * Fetch all relevant filters from the request.
     */
    public function getFilters(): array
    {
        return array_filter($this->request->only(array_keys($this->filtersArray)));
    }

    /**
     * Filter the query by a given search column.
     */
    protected function search(string $search): Builder
    {
        $column = is_numeric($search) 
            ? $this->request->get('searchNumericColumn', 'id')
            : $this->request->get('searchColumn', 'name');

        return $this->builder->where($column, 'LIKE', $search.'%');
    }

    /**
     * Filter the query by a given column for ordering.
     */
    protected function orderBy(string $column): Builder
    {
        return $this->builder->orderBy($column, $this->request->get('orderByDirection', 'asc'));
    }

    /**
     * Get the filters available for the resource.
     */
    protected function filters(): array
    {
        return [];
    }
}
