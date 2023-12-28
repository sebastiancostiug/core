<?php
/**
 *
 * @package     slim-base
 *
 * @subpackage  LoadConsoleEnvironment
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 * @see
 *
 * @since       2023-12-02
 *
 */

namespace seb\bootstrap\foundation\bootstrappers;

use seb\bootstrap\foundation\Bootstrapper;

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
        if (!class_exists('\seb\console\Console')) {
            return;
        }
        $console = new \seb\console\Console();

        $this->app->bind(\seb\console\Console::class, fn() => $console);
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
