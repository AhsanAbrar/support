<?php

namespace AhsanDev\Support\Authorization;

use App\Models\Permission;
use AhsanDev\Support\Authorization\Exceptions\PermissionDoesNotExist;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Gate;

class Authorization
{
    /**
     * @var \Illuminate\Contracts\Auth\Access\Gate
     */
    protected $gate;

    /**
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * The currently authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Access\Authorizable
     */
    protected $user;

    /**
     * The current ability to check.
     *
     * @var string
     */
    protected $ability;

    /**
     * Authorization constructor.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @param  \Illuminate\Cache\CacheManager  $cacheManager
     */
    public function __construct(Gate $gate, CacheManager $cache)
    {
        $this->gate = $gate;
        $this->cache = $cache;
    }

    /**
     * Handle Permissions.
     *
     * @return void
     */
    public function handle()
    {
        $this->gate->before(function (Authorizable $user, string $ability) {
            $this->user = $user;
            $this->ability = $ability;

            if ($user->isSuperAdmin()) {
                return true;
            }

            return $this->checkPermission();
        });
    }

    /**
     * Check for permission.
     *
     * @return bool
     */
    protected function checkPermission()
    {
        return $this->cache->rememberForever("user:{$this->user->id}:permission:{$this->ability}", function () {
            if ($this->user->hasPermissions()) {
                return $this->user->getPermissions()
                            ->contains('name', $this->ability);
            }

            return $this->checkPermissionAgainstRoles();
        });
    }

    /**
     * Check for permission.
     *
     * @return bool
     */
    protected function checkPermissionAgainstRoles()
    {
        return (bool) $this->getPermission()
                        ->roles
                        ->intersect($this->user->getRoles())
                        ->count();
    }

    /**
     * Find Permission
     *
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws PermissionDoesNotExist
     */
    protected function getPermission()
    {
        $permission = $this->cache->rememberForever("permission:{$this->ability}", function () {
            return Permission::whereName($this->ability)
                            ->with('roles')
                            ->first();
        });

        if (! $permission) {
            throw new PermissionDoesNotExist("There is no permission named `{$this->ability}`");
        }

        return $permission;
    }
}
