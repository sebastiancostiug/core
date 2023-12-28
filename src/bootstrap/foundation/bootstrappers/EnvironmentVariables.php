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

namespace seb\bootstrap\foundation\bootstrappers;

use seb\bootstrap\foundation\Bootstrapper;

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
        $env = file_get_contents(base_path('.env'));
        $lines = explode("\n", $env);

        foreach ($lines as $line) {
            preg_match('/([^#]+)\=(.*)/', $line, $matches);
            if (isset($matches[2])) {
                putenv(trim($line));
            }
        }
    }
}
