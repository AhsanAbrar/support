<?php

namespace AhsanDev\Support\Authorization\Http\Controllers;

use App\Support\Authorization\AuthorizesRequests;
use Illuminate\Routing\ControllerMiddlewareOptions;

class AuthorizeController
{
    use AuthorizesRequests;

    /**
     * The authorization controller name.
     *
     * @var string
     */
    protected $name;

    /**
     * Skip the map of resource methods of ability names.
     *
     * @var array
     */
    protected $excludeResource = [];

    /**
     * The middleware registered on the controller.
     *
     * @var array
     */
    protected $middleware = [];

    public function __construct()
    {
        $this->authorizeResource($this->name);
    }

    /**
     * Register middleware on the controller.
     *
     * @param  \Closure|array|string  $middleware
     * @param  array  $options
     * @return \Illuminate\Routing\ControllerMiddlewareOptions
     */
    public function middleware($middleware, array $options = [])
    {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }

        return new ControllerMiddlewareOptions($options);
    }

    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
}
