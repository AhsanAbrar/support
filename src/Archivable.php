<?php

namespace AhsanDev\Support;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withArchived(bool $withArchived = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder onlyArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withoutArchived()
 */
trait Archivable
{
    /**
     * Boot the archivable trait for a model.
     */
    public static function bootArchivable(): void
    {
        static::addGlobalScope(new ArchivingScope);
    }

    /**
     * Initialize the archivable trait for an instance.
     */
    public function initializeArchivable(): void
    {
        if (! isset($this->casts[$this->getArchivedAtColumn()])) {
            $this->casts[$this->getArchivedAtColumn()] = 'datetime';
        }
    }

    /**
     * Initialize the archivable trait for an instance.
     */
    public function archive(): bool
    {
        return $this->update([$this->getArchivedAtColumn() => now()]);
    }

    /**
     * Restore an archived model instance.
     */
    public function unarchive(): bool
    {
        $this->{$this->getArchivedAtColumn()} = null;

        return $this->save();
    }

    /**
     * Get the name of the "archived at" column.
     */
    public function getArchivedAtColumn(): string
    {
        return defined(static::class.'::ARCHIVED_AT') ? static::ARCHIVED_AT : 'archived_at';
    }

    /**
     * Get the fully qualified "archived at" column.
     */
    public function getQualifiedArchivedAtColumn(): string
    {
        return $this->qualifyColumn($this->getArchivedAtColumn());
    }
}
