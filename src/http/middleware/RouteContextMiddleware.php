<?php
/**
 *
 * @package     slim-base
 *
 * @subpackage  RouteContextMiddleware
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 * @see
 *
 * @since       2024-01-27
 *
 */

namespace core\http\middleware;

use core\http\RequestInput;
use core\http\routing\Redirect;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest as Request;

/**
 * RouteContextMiddleware class
 */
class RouteContextMiddleware
{
    /**
     * __invoke()
     *
     * @param  Request $request Request
     * @param  mixed   $handler
     * @return void
     */
    public function __invoke(Request $request, mixed $handler)
    {
        $routeContext = \Slim\Routing\RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        throw_when(empty($route), new \Exception('Route not found'));

        app()->bind(Redirect::class, fn(Psr17Factory $factory) => new Redirect($factory));

        $input = new RequestInput($request, $route);
        app()->bind(RequestInput::class, $input);

        return $handler->handle($request);
    }
}
