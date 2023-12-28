<?php
/**
 * @package     Slim 4 Base
 *
 * @subpackage  Core - bootstrappers
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    bootstrapper
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023-10-17
 */

namespace seb\bootstrap\foundation\bootstrappers;

use seb\bootstrap\foundation\Bootstrapper;
use seb\bootstrap\foundation\Kernel;

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
        $kernel = $this->app->getContainer()->get(Kernel::class);

        $this->app->getContainer()->set('middleware', fn() => [
            'global' => $kernel->middleware,
            'api'    => $kernel->middlewareGroups['api'],
            'web'    => $kernel->middlewareGroups['web'],
        ]);
    }
}
