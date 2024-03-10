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
 * @category    debugging
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
            $payload['name'] = method_exists($exception, 'getName') ? $exception->getName() : 'Exception';
            $payload['error'] = $exception->getMessage();
            // $input = app()->resolve(RequestInput::class) ?? null;

            $userId = app()->user?->id;

            if ($displayErrorDetails) {
                $payload['timestamp']   = date('Y-m-d H:i:s');
                $payload['http_method'] = $_SERVER['REQUEST_METHOD'];
                $payload['url']         = $_SERVER['REQUEST_URI'];
                $payload['user_agent']  = $_SERVER['HTTP_USER_AGENT'];
                $payload['user_id']     = $userId ?? 0;
                // $payload['input']       = $input->all();
                $payload['code']        = $exception->getCode();
                $payload['file']        = $exception->getFile();
                $payload['line']        = $exception->getLine();

                // file context around the error line to help debugging
                $fileContent = file($payload['file']);
                // subtract 1 from the line number to get the array index
                $errorLine = $payload['line'] - 1;
                $startLine = max(0, $errorLine - 6);
                $endLine = min(count($fileContent), $errorLine + 3);

                $lines = array_slice($fileContent, $startLine, $endLine - $startLine + 1);
                foreach ($lines as $i => $line) {
                    $lineNumber = $startLine + $i + 1;
                    $highlight = $lineNumber === $payload['line'] ? '>>> ' : '    ';
                    // Replace spaces with non-breaking spaces to keep indentation in HTML
                    $line = str_replace(' ', '&nbsp;', $line);
                    $highlight = str_replace(' ', '&nbsp;', $highlight);
                    // <br> tags are added to the end of each line to prevent the browser from collapsing them
                    $lines[$i] = $highlight . $lineNumber . ': ' . $line . '<br/>';
                }

                // Add the file context to the payload as a single string
                $payload['context']  = $lines;
                $payload['previous'] = $exception->getPrevious();
                $payload['debug']    = method_exists($exception, 'getErrors') ? $exception->getErrors() : [];
                $payload['trace']    = $exception->getTrace();
            }

            if ($logErrors) {
                $errorDetails = $logErrorDetails ? [
                    'TIMESTAMP: ' . date('Y-m-d H:i:s'),
                    'HTTP_METHOD: ' . $_SERVER['REQUEST_METHOD'],
                    'URL: ' . $_SERVER['REQUEST_URI'],
                    'USER_AGENT: ' . $_SERVER['HTTP_USER_AGENT'],
                    'USER_ID: ' . $userId ?? 0 ,
                    // 'INPUT: ' . json_encode($input->all(), JSON_UNESCAPED_SLASHES),
                    'CODE: ' . $exception->getCode(),
                    'FILE: ' . $exception->getFile(),
                    'LINE: ' . $exception->getLine(),
                    'PREVIOUS: ' . $exception->getPrevious(),
                    'DEBUG: ' . json_encode(method_exists($exception, 'getErrors') ? $exception->getErrors() : [], JSON_UNESCAPED_SLASHES),
                    'TRACE:',
                    $exception->getTraceAsString()
                ] : [];

                log_to_file(
                    'app_debug_log',
                    'ERROR: ' . $exception->getMessage(),
                    ...$errorDetails
                );
            }

            if (is_api_client()) {
                $response = new Response();
                $response->getBody()->write(
                    json_encode($payload, JSON_UNESCAPED_SLASHES)
                );

                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $view     = app()->resolve(View::class);
            $template = $view->randomPage('error/exception');
            if ($template) {
                return $view($template, $payload, 'blank')->withStatus(500);
            }

            return ($this->_defaultHandler)($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails);
        };
    }
}
