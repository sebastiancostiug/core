<?php
/**
 * @package     Slim 4 Base
 *
 * @subpackage  <Service Provider class>
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    Service Providers
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2022.11.10
 */

namespace core\providers;

use core\http\routing\Route;
use core\http\routing\RouteGroup;
use core\components\ServiceProvider;

/**
 * Service Provider class
 */
class RoutesProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        Route::setup($this->app);

        $this->bind(RouteGroup::class, fn () => new RouteGroup($this->app));
    }

    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
        $this->apiRouteGroup()->register();
        $this->webRouteGroup()->register();
    }

/**
 * apiRouteGroup()
 *
 * @return mixed
 */
    public function apiRouteGroup() : RouteGroup
    {
        $get = routes_path('api.php');
        $add = $this->resolve('middleware');
        $api = $this->resolve(RouteGroup::class);

        return $api->routes($get)->prefix(env('API_PREFIX', 'api'))->middleware([
            ...$add['api'],
            ...$add['global']
        ]);
    }

/**
 * webRouteGroup()
 *
 * @return mixed
 */
    public function webRouteGroup() : RouteGroup
    {
        $get = routes_path('web.php');
        $add = $this->resolve('middleware');
        $web = $this->resolve(RouteGroup::class);

        return $web->routes($get)->prefix(env('WEB_PREFIX', ''))->middleware([
        ...$add['web'],
        ...$add['global']
        ]);
    }
}
