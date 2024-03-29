<?php
/**
 * @package     Core
 *
 * @subpackage  LoadCsrf
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    bootstrap
 *
 * @since       2024-01-31
 */

namespace core\bootstrap;

use core\foundation\Bootstrapper;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\Csrf\Guard;

/**
 * LoadCsrf class
 */
class LoadCsrf extends Bootstrapper
{
    /**
     * Boot the LoadCsrf class.
     *
     * This method loads the Csrf middleware.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('csrf', function (Psr17Factory $factory) {
            $guard = new Guard($factory);
            $guard->setPersistentTokenMode(true);

            return $guard;
        });
    }
}
