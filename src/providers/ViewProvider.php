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

use core\components\ServiceProvider;
use Nyholm\Psr7\Factory\Psr17Factory;
use core\http\View;

/**
 * Service Provider class
 */
class ViewProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        $this->bind(View::class, fn () => new View(new Psr17Factory));
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
