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
class EnvironmentVariables extends Bootstrapper
{
    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
        try {
            if (file_exists(base_path('.env'))) {
                $env = file_get_contents(base_path('.env'));
                $lines = explode("\n", $env);

                foreach ($lines as $line) {
                    preg_match('/([^#]+)\=(.*)/', $line, $matches);
                    if (isset($matches[2])) {
                        putenv(trim($line));
                    }
                }
            }
        } catch (\Exception $e) {
            // Do nothing
        }
    }
}
