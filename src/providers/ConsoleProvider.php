<?php
/**
 *
 * @package     Core
 *
 * @subpackage  ConsoleProvider
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    providers
 *
 * @since       2023-12-02
 *
 */

namespace core\providers;

use common\Collection;
use core\foundation\Kernel;
use console\Console;
use core\components\ServiceProvider;

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
