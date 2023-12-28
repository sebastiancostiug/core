<?php
/**
 * @package     Core
 *
 * @subpackage  Bootstrapper
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    foundation
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023-10-17
 */

namespace core\foundation;

use core\foundation\App;
use common\Collection;

/**
 * Bootstrapper class
 */
class Bootstrapper
{
    /**
     * @var App $app App
     */
    public App $app;

    /**
     * @var Kernel $kernel Kernel
     */
    public Kernel $kernel;

    /**
     * __construct
     *
     * @param App    $app    App
     * @param Kernel $kernel Kernel
     *
     * @return void
     */
    public function __construct(App &$app, Kernel &$kernel)
    {
        $this->app    = $app;
        $this->kernel = $kernel;
    }

    /**
     * setup
     *
     * @param App    $app           App
     * @param Kernel $kernel        Kernel
     * @param array  $bootstrappers Loaders - a list of strings to class paths
     *
     * @return void
     */
    final public static function setup(App &$app, Kernel &$kernel, array $bootstrappers)
    {
        $bootstrappers = new Collection($bootstrappers);

        $bootstrappers->map(fn($bootstrapper) => new $bootstrapper($app, $kernel))
            ->each(fn(Bootstrapper $boot) => $boot->beforeBoot())
            ->each(fn(Bootstrapper $boot) => $boot->boot())
            ->each(fn(Bootstrapper $boot) => $boot->afterBoot());
    }

    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * beforeBoot()
     *
     * @return void
     */
    public function beforeBoot()
    {
        //
    }

    /**
     * afterBoot()
     *
     * @return void
     */
    public function afterBoot()
    {
        //
    }
}
