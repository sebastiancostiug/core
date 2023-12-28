<?php
/**
 *
 * @package     Core
 *
 * @subpackage  Load Console Environment bootstrapper
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    bootstrapper
 *
 * @since       2023-12-02
 *
 */

namespace core\bootstrap;

use core\foundation\Bootstrapper;

/**
 * LoadConsoleEnvironment class
 */
class LoadConsoleEnvironment extends Bootstrapper
{
    /**
     * Boot the application's console environment.
     *
     * @return void
     */
    public function beforeBoot()
    {
        if (!class_exists('\console\Console')) {
            return;
        }
        $console = new \console\Console();

        $this->app->bind(\console\Console::class, fn() => $console);
    }

    /**
     * Boot the application's console environment.
     *
     * @return void
     */
    public function boot()
    {
    }
}
