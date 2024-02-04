<?php
/**
 * @package     Core
 *
 * @subpackage  Load Aliases class
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    bootstrap
 *
 * @since       2024-01-28
 */

namespace core\bootstrap;

use core\foundation\Bootstrapper;

/**
 * LoadAliases class
 */
class LoadAliases extends Bootstrapper
{
    /**
     * Boot the LoadAliases class.
     *
     * This method loads the aliases defined in the configuration file and registers them using the class_alias function.
     *
     * @return void
     */
    public function boot()
    {
        $aliases = config('aliases');

        array_walk($aliases, fn ($path, $alias) => class_alias($path, $alias, true));
    }
}
