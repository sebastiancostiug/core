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
            $payload = [
                'error'    => $exception->getMessage(),
                'code'     => $exception->getCode(),
            ];
            $input = app()->resolve(RequestInput::class);

            if ($displayErrorDetails) {
                $payload['input']    = $input->all();
                $payload['file']     = $exception->getFile();
                $payload['line']     = $exception->getLine();
                $payload['previous'] = $exception->getPrevious();
                $payload['trace']    = $exception->getTrace();
            }

            log_to_file(
                'app_debug_log',
                'ERROR: ' . $exception->getMessage(),
                'CODE: ' . $exception->getCode(),
                'FILE: ' . $exception->getFile(),
                'LINE: ' . $exception->getLine(),
                'USER ID: ' . Auth::user()['id'] ?? 'Guest',
                'INPUT: ' . json_encode($input->all(), JSON_UNESCAPED_SLASHES),
                'TRACE:',
                $exception->getTraceAsString()
            );

            if (static::isApiClient($request)) {
                $response = new Response();
                $response->getBody()->write(
                    json_encode($payload, JSON_UNESCAPED_SLASHES)
                );

                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            if (file_exists(views_path('debug.php'))) {
                $view = app()->resolve(View::class);

                return $view('debug', $payload)->withStatus(500);
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
