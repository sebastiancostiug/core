<?php
/**
 * @package     Core
 *
 * @subpackage  App class
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    foundation
 *
 * @since       2023-12-02
 */

namespace core\foundation;

/**
 * Class App
 *
 * This class extends the \Slim\App class and provides additional methods for booting the application,
 * calling dependencies, and resolving dependencies from the container.
 */
class App extends \Slim\App
{
    /**
     * Magic method to retrieve inaccessible properties.
     *
     * @param string $name The name of the property to retrieve.
     *
     * @return mixed The value of the property.
     */
    public function __get($name)
    {
        try {
            return $this->resolve($name);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Magic method to handle dynamic method calls.
     *
     * @param string $name      The name of the method being called.
     * @param array  $arguments The arguments passed to the method.
     *
     * @return mixed The result of the method call.
     */
    public function __call($name, array $arguments): mixed
    {
        try {
            $resolved = $this->resolve($name);

            if (is_callable($resolved)) {
                return call_user_func_array($resolved, $arguments);
            }

            // If the resolved value is not callable, try to resolve it as a property.
            return $this->__get($name);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if the application was booted via console and run the corresponding command.
     *
     * @return boolean Returns true if the application was booted via console, false otherwise.
     */
    public function isConsoleApp()
    {
        return $this->has('consoleApp') ? $this->resolve('consoleApp') : false;
    }

    /**
     * Check if the application was booted via HTTP and run the corresponding request.
     *
     * @return boolean Returns true if the application was booted via HTTP, false otherwise.
     */
    public function isWebApp()
    {
        return $this->has('webApp') ? $this->resolve('webApp') : false;
    }

    /**
     * Call a callable with resolved dependencies from the container.
     *
     * @param mixed ...$parameters The parameters to be passed to the callable.
     *
     * @return mixed The result of the callable.
     */
    public function call(...$parameters)
    {
        return $this->getContainer()->call(...$parameters);
    }

    /**
     * Check if the container has a specific entry.
     *
     * @param mixed ...$parameters The parameters to check in the container.
     *
     * @return boolean Returns true if the container has the specified entry, false otherwise.
     */
    public function has(...$parameters)
    {
        return $this->getContainer()->has(...$parameters);
    }

    /**
     * Create an instance of a class from the container.
     *
     * @param  ...$parameters The parameters to create the instance.
     *
     * @return mixed The created instance.
     */
    public function make(...$parameters)
    {
        return $this->getContainer()->make(...$parameters);
    }

    /**
     * Bind a value or a closure to the container.
     *
     * @param mixed ...$parameters The parameters to bind to the container.
     *
     * @return mixed The bound value or closure.
     */
    public function bind(...$parameters)
    {
        return $this->getContainer()->set(...$parameters);
    }

    /**
     * Resolve a dependency from the container.
     *
     * @param mixed ...$parameters The parameters to resolve the dependency.
     *
     * @return mixed The resolved dependency.
     */
    public function resolve(...$parameters)
    {
        return $this->getContainer()->get(...$parameters);
    }
}
