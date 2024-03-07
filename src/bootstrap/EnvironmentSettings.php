<?php
/**
 * @package     Core
 *
 * @subpackage  Environment Variables bootstrapper
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    bootstrapper
 *
 * @since       2023-10-17
 */

namespace core\bootstrap;

use core\foundation\Bootstrapper;

/**
 * EnvironmentVariables class
 */
class EnvironmentSettings extends Bootstrapper
{
    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
        date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

        if (!dev_env()) {
            // Production
            error_reporting(0);
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
        }
    }
}
