<?php
/**
 * @package     Core
 *
 * @subpackage  Redirect Service Provider class
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    Service Providers
 *
 * @since       2024.02.20
 */

namespace core\providers;

use core\components\ServiceProvider;
use core\http\routing\Routing;

/**
 * Service Provider class
 */
class RedirectProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('routing', fn () => new Routing());
    }

    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
    }
}
