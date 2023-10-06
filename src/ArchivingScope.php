<?php

namespace AhsanDev\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ArchivingScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected $extensions = ['Archive', 'UnArchive', 'WithArchived', 'WithoutArchived', 'OnlyArchived'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNull($model->getQualifiedArchivedAtColumn());
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder): void
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Get the "archived at" column for the builder.
     */
    protected function getArchivedAtColumn(Builder $builder): string
    {
        if (count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedArchivedAtColumn();
        }

        return $builder->getModel()->getArchivedAtColumn();
    }

    /**
     * Add the archive extension to the builder.
     */
    protected function addArchive(Builder $builder): void
    {
        $builder->macro('archive', function (Builder $builder) {
            $column = $this->getArchivedAtColumn($builder);

            return $builder->update([
                $column => $builder->getModel()->freshTimestampString(),
            ]);


            $builder->withArchived();

            return $builder->update([$builder->getModel()->getArchivedAtColumn() => null]);
        });
    }

    /**
     * Add the unarchive extension to the builder.
     */
    protected function addUnArchive(Builder $builder): void
    {
        $builder->macro('unarchive', function (Builder $builder) {
            $builder->withArchived();

            return $builder->update([$builder->getModel()->getArchivedAtColumn() => null]);
        });
    }

    /**
     * Add the with-archived extension to the builder.
     */
    protected function addWithArchived(Builder $builder): void
    {
        $builder->macro('withArchived', function (Builder $builder, $withArchived = true) {
            if (! $withArchived) {
                return $builder->withoutArchived();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-archived extension to the builder.
     */
    protected function addWithoutArchived(Builder $builder): void
    {
        $builder->macro('withoutArchived', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNull(
                $model->getQualifiedArchivedAtColumn()
            );

            return $builder;
        });
    }

    /**
     * Add the only-archived extension to the builder.
     */
    protected function addOnlyArchived(Builder $builder): void
    {
        $builder->macro('onlyArchived', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNotNull(
                $model->getQualifiedArchivedAtColumn()
            );

            return $builder;
        });
    }
}
