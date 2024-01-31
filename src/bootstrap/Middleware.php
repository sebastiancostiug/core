<?php
/**
 * @package     Core
 *
 * @subpackage  Middleware bootstrapper
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    bootstrappers
 *
 * @since       2023-10-17
 */

namespace core\bootstrap;

use common\Collection;
use core\foundation\Bootstrapper;
use core\foundation\Kernel;

/**
 * Middleware class
 */
class Middleware extends Bootstrapper
{
    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
        $kernel = app()->resolve(Kernel::class);

        $applicationMiddleware = config('middleware');

        $globalMiddleware = [
            ...$kernel->middleware,
            ...$applicationMiddleware['global'],
        ];
        $apiMiddleware = [
            ...$kernel->middlewareGroups['api'],
            ...$applicationMiddleware['api'],
        ];
        $webMiddleware = [
            ...$kernel->middlewareGroups['web'],
            ...$applicationMiddleware['web'],
        ];
        $middleware = [
            ...$globalMiddleware,
            ...$apiMiddleware,
            ...$webMiddleware,
        ];

        $middlewareCollection = new Collection($middleware);
        $middlewareCollection
            ->filter(fn($guard) => class_exists($guard))
            ->each(fn($guard) => app()->bind($guard, new $guard));

        app()->bind('middleware', fn() => [
            'global' => $globalMiddleware,
            'api'    => $apiMiddleware,
            'web'    => $webMiddleware,
        ]);
    }
}
