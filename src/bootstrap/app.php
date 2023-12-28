<?php
/**
 * @package     Slim 4 Base
 *
 * @subpackage  Bootstrap
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
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Timezone
date_default_timezone_set(config('app.timezone'));

use seb\bootstrap\foundation\AppFactory as App;
use seb\http\HttpKernel;

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

$app = App::create();

$http_kernel = new HttpKernel($app);
$app->bind(HttpKernel::class, $http_kernel);

if (class_exists('seb\console\ConsoleKernel')) {
    $console_kernel = new \seb\console\ConsoleKernel($app);
    $app->bind(\seb\console\ConsoleKernel::class, $console_kernel);
}

return $app;
