<?php
/**
 * @package     Core
 *
 * @subpackage  Kernel
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    kernel
 *
 * @since       2023-10-17
 */

namespace core\foundation;

use core\foundation\App;

/**
 * Kernel class
 */
class Kernel
{
    /**
     * @var App $app App
     */
    public App $app;

    /**
     * @var array $bootstrappers Register application bootstrap loaders
     */
    public array $bootstrappers = [];

    /**
     * __construct()
     *
     * @param App $app App
     *
     * @return void
     */
    public function __construct(App &$app)
    {
        $this->app = $app;
    }

    /**
     * bootstrap()
     *
     * @return void
     */
    public function bootstrap()
    {
        $app           = $this->getApp();
        $kernel        = $this->getKernel();
        $bootstrappers = $this->bootstrappers;

        Bootstrapper::setup($app, $kernel, $bootstrappers);
    }

    /**
     * getKernel
     *
     * @return self
     */
    public function getKernel(): self
    {
        return $this;
    }

    /**
     * getApp
     *
     * @return App
     */
    public function getApp(): App
    {
        return $this->app;
    }
}
