<?php
/**
 *
 * @package     Core
 *
 * @subpackage  Environment Detector bootstrapper
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
use core\foundation\Kernel;
use core\http\HttpKernel;

/**
 * Class EnvironmentDetector
 *
 * This class is responsible for detecting the environment (HTTP or Console) and binding the appropriate application instance to the container.
 */
class EnvironmentDetector extends Bootstrapper
{
    const HTTP_ENV = HttpKernel::class;
    const CONSOLE_ENV = class_exists('\seb\console\ConsoleKernel') ? \seb\console\ConsoleKernel::class : null;

    /**
     * Boot the environment detector.
     *
     * This method detects the current environment (HTTP or Console) by comparing the class names of the kernel instances.
     * It then binds the appropriate application instance to the container using the 'webApp' and 'consoleApp' keys.
     * Finally, it binds the kernel instance itself to the container using the Kernel class.
     *
     * @return void
     */
    public function boot()
    {
        $http    = class_basename(self::HTTP_ENV) === class_basename($this->kernel);
        $console = class_basename(self::CONSOLE_ENV) === class_basename($this->kernel);

        $this->app->bind('webApp', fn() => $http);
        $this->app->bind('consoleApp', fn() => $console);

        $this->app->bind(Kernel::class, $this->kernel);
    }
}
