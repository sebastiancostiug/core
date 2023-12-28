<?php
/**
 * @package     Slim 4 Base
 *
 * @subpackage  Core - routing
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    config
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2022.11.10
 */

namespace core\http\routing;

/**
 * Route class
 */
class Route
{
    /**
     * @var mixed $app
     */
    public static $app;

    /**
     * setup()
     *
     * @param mixed $app App
     *
     * @return mixed
     */
    public static function setup(mixed &$app)
    {
        self::$app = $app;

        return $app;
    }

    /**
     * __callStatic()
     *
     * @param string $verb       Verb
     * @param array  $parameters Parameters
     *
     * @return mixed
     */
    public static function __callStatic($verb, array $parameters)
    {
        $app = self::$app;
        [$route, $action, $type] = $parameters;

        if (!config("app.enabled.$type")) {
            $action = 'DefaultController@catchall';
        }

        self::validation($route, $verb, $action);

        is_callable($action) ? $app->$verb($route, $action) : $app->$verb($route, self::resolveViaController($type, $action));
    }

    /**
     * resolveViaController()
     *
     * @param string $type   Type
     * @param string $action Action
     *
     * @return array
     */
    public static function resolveViaController($type, $action)
    {
        $module = str_before($action, '@');
        $method = str_after($action, '@');

        if (str_is('*|*', $module)) {
            $namespace = str_before($module, '|');
            $class = str_after($module, '|');
        } else {
            $namespace = 'app';
            $class = $module;
        }
        $controller = config("routing.controllers.$type.$namespace") . $class;

        return [$controller, $method];
    }

    /**
     * validation()
     *
     * @param string $route  Route
     * @param string $verb   Verb
     * @param string $action Action
     *
     * @return void
     * @throws 'Uresolvable action' Exception
     */
    protected static function validation($route, $verb, $action)
    {
        $exception = 'Unresolvable Route Callback/Controller action';
        $context = json_encode(compact('route', 'action', 'verb'));

        $fails = !((is_callable($action)) or (is_string($action) and str_is('*@*', $action)));

        throw_when($fails, $exception . $context);
    }
}
