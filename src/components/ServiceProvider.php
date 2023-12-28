<?php
/**
 * @package     Core
 *
 * @subpackage  Service Provider
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    Service Providers
 *
 * @since       2022.11.10
 */

namespace core\components;

use Psr\Container\ContainerInterface;
use core\foundation\App;
use common\Collection;

/**
 * Service Provider class
 */
abstract class ServiceProvider
{
    /**
     * @var mixed $app
     */
    public $app;

    /**
     * @var ContainerInterface $container
     */
    public $container;

    /**
     * constructor
     *
     * @param mixed $app App
     */
    final public function __construct(mixed &$app)
    {
        $this->app = $app;
        $this->container = $this->app->getContainer();
    }

    /**
     * register()
     *
     * @return void
     */
    abstract public function register();

    /**
     * boot()
     *
     * @return void
     */
    abstract public function boot();

    /**
     * bind()
     * Registers a key in the container
     *
     * @param string   $key        Key
     * @param callable $resolvable Resolvable
     *
     * @return void
     */
    public function bind($key, callable $resolvable)
    {
        $this->container->set($key, $resolvable);
    }

    /**
     * resolve()
     * Gets registered key from container
     *
     * @param string $key Key
     *
     * @return mixed
     */
    public function resolve($key)
    {
        return $this->container->get($key);
    }

    /**
     * setup()
     *
     * @param App   $app       App
     * @param array $providers Providers
     *
     * @return void
     */
    final public static function setup(App &$app, array $providers)
    {
        $providers = new Collection($providers);
        $providers = $providers->filter(fn ($provider) => class_exists($provider))
            ->map(fn ($provider) => new $provider($app))
            ->each(fn(ServiceProvider $provider) => $provider->register())
            ->each(fn(ServiceProvider $provider) => $provider->boot());
    }
}
