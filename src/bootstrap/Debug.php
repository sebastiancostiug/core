<?php
/**
 * @package     Core
 *
 * @subpackage  Debug bootstrapper
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

use core\core\middleware\ApiDebugMiddleware;
use core\foundation\Bootstrapper;

/**
 * Debug class
 */
class Debug extends Bootstrapper
{
    /**
     * boot() - Boot the debug bootstrapper.
     *
     * @return void Returns nothing.
     */
    public function boot()
    {
        $display_error_details = dev_env();
        $log_errors            = true;
        $log_error_details     = true;

        $errorMiddleware = $this->app->addErrorMiddleware(
            $display_error_details,
            $log_errors,
            $log_error_details
        );

        if (!self::inBrowser()) {
            $debug = new ApiDebugMiddleware;
            $errorMiddleware->setDefaultErrorHandler($debug->handler);
        }
    }

    /**
     * inBrowser() - Check if the request is made from a browser.
     *
     * @return boolean True if the request is made from a browser, false otherwise.
     */
    private static function inBrowser()
    {
        $browsers = [
            '/msie/i'    => 'Internet explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i'  => 'Safari',
            '/chrome/i'  => 'Chrome',
            '/edge/i'    => 'Edge',
            '/opera/i'   => 'Opera',
            '/mobile/i'  => 'Mobile browser',
        ];

        foreach ($browsers as $regex => $value) {
            if (preg_match($regex, env('HTTP_USER_AGENT', ''))) {
                return true;
            }
        }

        return false;
    }
}
