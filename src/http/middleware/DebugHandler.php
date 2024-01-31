<?php
/**
 * @package     Core
 *
 * @subpackage  Middleware
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    Middleware
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2022.11.10
 */

namespace core\http\middleware;

use core\http\RequestInput;
use core\http\View;
use Nyholm\Psr7\ServerRequest as Request;
use Nyholm\Psr7\Response;
use Slim\Handlers\ErrorHandler;
use Throwable;

/**
 * DebugHandler
 */
class DebugHandler
{
    /**
     * @var function handler
     */
    public $handler;

    /**
     * @var ErrorHandler|callable $defaultHandler Default Handler
     */
    private $_defaultHandler;

    /**
     * @var RequestInput $input Request Input
     */
    private $_input;

    /**
     * __construct()
     *
     * @param rrorHandler|callable $defaultHandler Default Handler
     *
     * @return void
     */
    public function __construct(ErrorHandler|callable $defaultHandler)
    {
        $this->_defaultHandler = $defaultHandler;


        $this->handler = function (
            Request $request,
            Throwable $exception,
            bool $displayErrorDetails,
            bool $logErrors,
            bool $logErrorDetails
        ) {
            // $routeContext = \Slim\Routing\RouteContext::fromRequest($request);
            // $route        = $routeContext->getRoute();
            // $this->_input = new RequestInput($request, $route);

            $payload = [
                'error'    => $exception->getMessage(),
                'code'     => $exception->getCode(),
            ];
            if ($displayErrorDetails) {
                $payload['file']     = $exception->getFile();
                $payload['line']     = $exception->getLine();
                $payload['previous'] = $exception->getPrevious();
                $payload['trace']    = $exception->getTrace();
            }

            if (!static::inBrowser()) {
                $response = new Response();
                $response->getBody()->write(
                    json_encode($payload, JSON_UNESCAPED_SLASHES)
                );

                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            if (file_exists(views_path('debug.php'))) {
                $view = app()->resolve(View::class);

                return $view('debug', $payload);
            }

            return ($this->_defaultHandler)($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails);
        };
    }

    /**
     * inBrowser() - Check if the request is made from a browser.
     *
     * @return boolean True if the request is made from a browser, false otherwise.
     */
    private static function inBrowser()
    {
        $browsers = [
            '/msie/i'    => 'Internet explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i'  => 'Safari',
            '/chrome/i'  => 'Chrome',
            '/edge/i'    => 'Edge',
            '/opera/i'   => 'Opera',
            '/mobile/i'  => 'Mobile browser',
        ];

        foreach ($browsers as $regex => $value) {
            if (preg_match($regex, env('HTTP_USER_AGENT', ''))) {
                return true;
            }
        }

        return false;
    }
}
