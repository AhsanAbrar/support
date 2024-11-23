<?php

namespace AhsanDev\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait Categorizable
{
    /**
     * Boot the categorizable trait for the model.
     */
    protected static function bootCategorizable(): void
    {
        static::saving(function ($model) {
            $model->setParentIdIfNotSet();
            $model->setSlugIfNotSet();
            $model->setKeyIfNotSet();
        });

        static::addGlobalScope('parent', function (Builder $builder) {
            $builder->where('parent_id', static::getParentId());
        });
    }

    /**
     * Set the parent ID if it's not already set.
     */
    protected function setParentIdIfNotSet(): void
    {
        if (is_null($this->parent_id)) {
            $this->parent_id = static::getParentId();
        }
    }

    /**
     * Set the slug if it's not already set.
     */
    protected function setSlugIfNotSet(): void
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    /**
     * Set the key if it's not already set.
     */
    protected function setKeyIfNotSet(): void
    {
        if (empty($this->key)) {
            $this->key = $this->name;
        }
    }

    /**
     * Get the parent category ID.
     */
    public static function getParentId(): ?int
    {
        return Cache::rememberForever(static::getParentCacheKey(), function () {
            return DB::table(static::getTableName())
                ->where('key', static::$parent)
                ->value('id');
        });
    }

    /**
     * Get the category ID based on key.
     */
    public static function getId(string $key): ?int
    {
        $parentId = static::getParentId();

        return Cache::rememberForever(static::getCategoryCacheKey($key), function () use ($key, $parentId) {
            return DB::table(static::getTableName())
                ->where('key', $key)
                ->where('parent_id', $parentId)
                ->value('id');
        });
    }

    /**
     * Get the cache key for a specific category by key and parent.
     */
    protected static function getCategoryCacheKey(string $key): string
    {
        return static::getParentCacheKey().':'.Str::slug($key);
    }

    /**
     * Get the cache key for the parent category.
     */
    protected static function getParentCacheKey(): string
    {
        return 'category:'.Str::slug(static::$parent);
    }

    /**
     * Get the table name for categories.
     */
    public static function getTableName(): string
    {
        return (new static)->getTable();
    }
}
