<?php
/**
 *
 * @package     slim-base
 *
 * @subpackage  ConsoleProvider
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

namespace seb\providers;

use seb\bootstrap\foundation\Kernel;
use seb\components\Collection;
use seb\components\ServiceProvider;
use seb\console\Console;

/**
 * ConsoleProvider class
 */
class ConsoleProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind('consoleApp', fn() => $console);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require routes_path('console.php');

        $routesCommands = Console::commands();

        $kernel = $this->app->resolve(Kernel::class);
        $kernelCommands = new Collection($kernel->commands);
        $kernelCommands = $kernelCommands->map(fn($command) => new $command());

        $commands = $routesCommands->merge($kernelCommands);

        $commands->map(fn($command) => Console::console()->add($command));
    }
}
