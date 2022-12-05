<?php

namespace AhsanDev\Support\Authorization;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;

trait Authorizable
{
    /**
     * The user is super admin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->id == 1;
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * The permissions that belong to the user.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Get User's Roles.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRoles()
    {
        return Cache::rememberForever("user:{$this->id}:roles", function () {
            return $this->roles;
        });
    }

    /**
     * Get User's Permissions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissions()
    {
        return Cache::rememberForever("user:{$this->id}:permissions", function () {
            return $this->permissions;
        });
    }

    /**
     * The user has direct permissions.
     *
     * @return bool
     */
    public function hasPermissions()
    {
        return $this->getPermissions()->isNotEmpty();
    }

    /**
     * assign role to the user.
     */
    public function assignRole($role)
    {
        return $this->roles()->sync(
            Role::whereName($role)->firstOrFail()
        );
    }

    /**
     * assign permission to the user.
     */
    public function assignPermission($permission)
    {
        return $this->permissions()->sync(
            Permission::whereName($permission)->firstOrFail()
        );
    }

    /**
     * A user has role.
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->getRoles()->contains('name', $role);
        }

        return (bool) $role->intersect($this->getRoles())->count();
    }
}
