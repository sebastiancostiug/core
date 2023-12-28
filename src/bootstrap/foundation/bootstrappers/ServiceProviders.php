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

namespace core\bootstrap\foundation\bootstrappers;

use core\bootstrap\foundation\Bootstrapper;
use core\components\ServiceProvider;

/**
 * ServiceProviders class
 */
class ServiceProviders extends Bootstrapper
{
    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;
        if ($app->isWebApp()) {
            $providers = config('providers.web');
        } elseif ($app->isConsoleApp()) {
            $providers = config('providers.console');
        }

        ServiceProvider::setup($app, $providers);
    }
}
