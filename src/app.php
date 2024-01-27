<?php
/**
 * @package     Core
 *
 * @subpackage  app
 *
 * @author      Sebastian Costiug, sebastian@overbyte.dev
 * @copyright   2019-2023 Sebastian Costiug <https://www.overbyte.dev>
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    config
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023.10.14
 */

// Autoload global dependencies and allow for auto-loading local dependencies via use
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

// Timezone
date_default_timezone_set(config('app.timezone'));

if (dev_env()) {
    // Development
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    // Production
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

// Create the application instance.
$app = \core\foundation\AppFactory::create();

$_SERVER['app'] = &$app;

$http_kernel = new core\http\HttpKernel($app);
$app->bind(core\http\HttpKernel::class, $http_kernel);

if (class_exists('\console\ConsoleKernel')) {
    $console_kernel = new \console\ConsoleKernel($app);
    $app->bind(\console\ConsoleKernel::class, $console_kernel);
}

if (!function_exists('app')) {
    /**
     * Get the application instance.
     *
     * @return mixed|\core\foundation\Application
     */
    function app()
    {
        return $_SERVER['app'];
    }
}

$app->addRoutingMiddleware();

return $app;
