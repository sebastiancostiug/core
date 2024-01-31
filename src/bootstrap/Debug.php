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

use core\foundation\Bootstrapper;
use core\http\middleware\DebugHandler;

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

        $defaultHandler = $errorMiddleware->getDefaultErrorHandler();

        $debug = new DebugHandler($defaultHandler);

        $errorMiddleware->setDefaultErrorHandler($debug->handler);
    }
}
