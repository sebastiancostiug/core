<?php
/**
 * @package     Core
 *
 * @subpackage  Http routing
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    routing
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023-10-17
 */

namespace core\http\routing;

use core\bootstrap\foundation\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * RouteGroup class
 */
class RouteGroup
{
    /**
     * @var App $app
     */
    public $app;

    /**
     * @var string $prefix
     */
    public $prefix;

    /**
     * @var mixed $routes
     */
    public $routes;

    /**
     * @var array $middleware
     */
    public $middleware = [];

    /**
     * __construct
     *
     * @param App $app App
     *
     * @return void
     */
    public function __construct(App &$app)
    {
        $this->app = $app;
    }

    /**
     * prefix
     *
     * @param string $prefix Prefix
     *
     * @return RouteGroup
     */
    public function prefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * routes
     *
     * @param string $path Path
     *
     * @return RouteGroup
     */
    public function routes($path = '')
    {
        $this->routes = $path;

        return $this;
    }

    /**
     * middleware
     *
     * @param array $middleware Middleware
     *
     * @return RouteGroup
     */
    public function middleware(array $middleware)
    {
        $this->middleware = $middleware;

        return $this;
    }

    /**
     * register
     *
     * @return void
     */
    public function register()
    {
        $group = $this->app->group($this->prefix, function (RouteCollectorProxy $group) {
            $app = Route::setup($group);

            require $this->routes;
        });

        array_walk($this->middleware, fn($middleware) => $group->add(new $middleware));

        Route::setup($this->app);
    }
}
