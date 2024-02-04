<?php
/**
 * @package     Core
 *
 * @subpackage  AppFactory class
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    foundation
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023-12-02
 */

namespace core\foundation;

use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;
use DI\Bridge\Slim\CallableResolver;
use DI\Bridge\Slim\ControllerInvoker;
use DI\Container;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use \Invoker\CallableResolver as InvokerCallableResolver;
use Slim\Interfaces\CallableResolverInterface;

/**
 * This class extends the Slim\Factory\AppFactory class and provides methods for creating an instance of the App class.
 * It includes a static method `createFromContainer` that creates an App instance using a ContainerInterface.
 * It also includes a static method `create` that creates an App instance with optional parameters.
 * The class also has a private method `createControllerInvoker` that creates a ControllerInvoker instance.
 */
class AppFactory extends \Slim\Factory\AppFactory
{
    /**
     * Creates an instance of the App class with optional dependencies.
     *
     * @param ResponseFactoryInterface|null      $responseFactory      The response factory implementation.
     * @param ContainerInterface|null            $container            The container implementation.
     * @param CallableResolverInterface|null     $callableResolver     The callable resolver implementation.
     * @param RouteCollectorInterface|null       $routeCollector       The route collector implementation.
     * @param RouteResolverInterface|null        $routeResolver        The route resolver implementation.
     * @param MiddlewareDispatcherInterface|null $middlewareDispatcher The middleware dispatcher implementation.
     *
     * @return App The created instance of the App class.
     */
    public static function create(
        ?ResponseFactoryInterface $responseFactory = null,
        ?ContainerInterface $container = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null
    ): App {
        $container = $container ?: new Container;

        $callableResolver = new InvokerCallableResolver($container);

        $container->set(CallableResolverInterface::class, new CallableResolver($callableResolver));
        $app = static::createFromContainer($container);

        $container->set(App::class, $app);

        $controllerInvoker = static::createControllerInvoker($container);
        $app->getRouteCollector()->setDefaultInvocationStrategy($controllerInvoker);

        return $app;
    }

    /**
     * Creates an instance of App from a container.
     *
     * @param ContainerInterface $container The container to create the App from.
     *
     * @return App The created App instance.
     */
    public static function createFromContainer(ContainerInterface $container): App
    {
        $responseFactory = $container->has(ResponseFactoryInterface::class)
        && (
            $responseFactoryFromContainer = $container->get(ResponseFactoryInterface::class)
        ) instanceof ResponseFactoryInterface ? $responseFactoryFromContainer : self::determineResponseFactory();

        $callableResolver = $container->has(CallableResolverInterface::class)
        && (
            $callableResolverFromContainer = $container->get(CallableResolverInterface::class)
        ) instanceof CallableResolverInterface ? $callableResolverFromContainer : null;

        $routeCollector = $container->has(RouteCollectorInterface::class)
        && (
            $routeCollectorFromContainer = $container->get(RouteCollectorInterface::class)
        ) instanceof RouteCollectorInterface ? $routeCollectorFromContainer : null;

        $routeResolver = $container->has(RouteResolverInterface::class)
        && (
            $routeResolverFromContainer = $container->get(RouteResolverInterface::class)
        ) instanceof RouteResolverInterface ? $routeResolverFromContainer : null;

        $middlewareDispatcher = $container->has(MiddlewareDispatcherInterface::class)
        && (
            $middlewareDispatcherFromContainer = $container->get(MiddlewareDispatcherInterface::class)
        ) instanceof MiddlewareDispatcherInterface ? $middlewareDispatcherFromContainer : null;

        return new App(
            $responseFactory,
            $container,
            $callableResolver,
            $routeCollector,
            $routeResolver,
            $middlewareDispatcher
        );
    }

    /**
     * Creates a ControllerInvoker instance.
     *
     * This method creates a ControllerInvoker instance by initializing the necessary resolvers and the Invoker.
     * The resolvers are used to resolve the dependencies of the controller methods.
     * The Invoker is responsible for invoking the controller methods with the resolved dependencies.
     *
     * @param ContainerInterface $container The container used for dependency injection.
     *
     * @return ControllerInvoker The created ControllerInvoker instance.
     */
    private static function createControllerInvoker(ContainerInterface $container): ControllerInvoker
    {
        $resolvers = [
            // Inject parameters by name first
            new AssociativeArrayResolver(),
            // Then inject services by type-hints for those that weren't resolved
            new TypeHintContainerResolver($container),
            // Then fall back on parameters default values for optional route parameters
            new DefaultValueResolver(),
        ];

        $invoker = new Invoker(new ResolverChain($resolvers), $container);

        return new ControllerInvoker($invoker);
    }
}
