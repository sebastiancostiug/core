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

use app\components\Auth;
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
            $payload['error'] = $exception->getMessage();
            $input = app()->resolve(RequestInput::class);

            if ($displayErrorDetails) {
                $payload['timestamp']    = date('Y-m-d H:i:s');
                $payload['http_method']  = $_SERVER['REQUEST_METHOD'];
                $payload['url']          = $_SERVER['REQUEST_URI'];
                $payload['user_agent']   = $_SERVER['HTTP_USER_AGENT'];
                $payload['user_id']      = Auth::user()['id'] ?? 'Guest';
                $payload['input']        = $input->all();
                $payload['session_data'] = $_SESSION;
                $payload['code']         = $exception->getCode();
                $payload['file']         = $exception->getFile();
                $payload['line']         = $exception->getLine();
                $payload['previous']     = $exception->getPrevious();
                $payload['trace']        = $exception->getTrace();
            }

            if ($logErrors) {
                $errorDetails = $logErrorDetails ? [
                    'TIMESTAMP: ' . date('Y-m-d H:i:s'),
                    'HTTP_METHOD: ' . $_SERVER['REQUEST_METHOD'],
                    'URL: ' . $_SERVER['REQUEST_URI'],
                    'USER_AGENT: ' . $_SERVER['HTTP_USER_AGENT'],
                    'USER_ID: ' . Auth::user()['id'] ?? 'Guest',
                    'INPUT: ' . json_encode($input->all(), JSON_UNESCAPED_SLASHES),
                    'SESSION_DATA: ' . json_encode($_SESSION, JSON_UNESCAPED_SLASHES),
                    'CODE: ' . $exception->getCode(),
                    'FILE: ' . $exception->getFile(),
                    'LINE: ' . $exception->getLine(),
                    'TRACE:',
                    $exception->getTraceAsString()
                ] : [];

                log_to_file(
                    'app_debug_log',
                    'ERROR: ' . $exception->getMessage(),
                    ...$errorDetails
                );
            }

            if (static::isApiClient($request)) {
                $response = new Response();
                $response->getBody()->write(
                    json_encode($payload, JSON_UNESCAPED_SLASHES)
                );

                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $view     = app()->resolve(View::class);
            $template = $view->randomPage('error/exception');
            if ($template) {
                return $view($template, $payload, null)->withStatus(500);
            }

            return ($this->_defaultHandler)($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails);
        };
    }

    /**
     * isApiClient() - Check if the request for an api call.
     *
     * @param Request $request The request object
     *
     * @return boolean True if the request is for an api call, false otherwise
     */
    private static function isApiClient(Request $request): bool
    {
        $acceptHeader = $request->getHeaderLine('Accept');

        return strpos($acceptHeader, 'application/json') !== false;
    }
}