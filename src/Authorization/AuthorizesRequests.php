<?php

namespace AhsanDev\Support\Authorization;

trait AuthorizesRequests
{
    /**
     * Authorize a resource action based on the incoming request.
     *
     * @param  string|null  $parameter
     * @param  string  $model
     * @return void
     */
    public function authorizeResource($parameter, $model = null)
    {
        $middleware = [];

        foreach ($this->resourceAbilityMap() as $method => $ability) {
            if (! in_array($ability, $this->excludeResource)) {
                $middleware["can:{$parameter}:{$ability}"][] = $method;
            }
        }

        foreach ($middleware as $middlewareName => $methods) {
            $this->middleware($middlewareName)->only($methods);
        }
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return [
            'index' => 'view',
            'show' => 'detail',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
        ];
    }

    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    protected function resourceMethodsWithoutModels()
    {
        return ['index', 'create', 'store'];
    }
}
