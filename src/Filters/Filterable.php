<?php

namespace AhsanDev\Support\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Filterable
 *
 * This trait provides a scope to apply filters to a query.
 */
trait Filterable
{
    /**
     * Apply all relevant filters.
     */
    public function scopeFilter(Builder $query, Filters $filters): Builder
    {
        return $filters->apply($query);
    }
}
