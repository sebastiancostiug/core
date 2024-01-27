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

        app()->bind('middleware', fn() => [
            'global' => $kernel->middleware,
            'api'    => $kernel->middlewareGroups['api'],
            'web'    => $kernel->middlewareGroups['web'],
        ]);
    }
}
