<?php
/**
 * @package     Core
 *
 * @subpackage  Route Context Middleware
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    middleware
 *
 * @since       2024-01-27
 */

namespace core\http\middleware;

use core\http\RequestInput;
use core\http\routing\Redirect;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * RouteContextMiddleware class
 */
class RouteContextMiddleware
{
    /**
     * __invoke()
     *
     * @param  Request $request Request
     * @param  Handler $handler Handler
     *
     * @return mixed
     */
    public function __invoke(Request $request, Handler $handler)
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
