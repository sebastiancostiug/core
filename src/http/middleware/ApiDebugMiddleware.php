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

namespace core\core\middleware;

use Nyholm\Psr7\ServerRequest as Request;
use Nyholm\Psr7\Response;
use Throwable;

/**
 * ApiDebugMiddleware
 */
class ApiDebugMiddleware
{
    /**
     * @var function handler
     */
    public $handler;

    /**
     * __construct()
     *
     * @return void
     */
    public function __construct()
    {
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
            if ($displayErrorDetails) {
                $payload['file']     = $exception->getFile();
                $payload['line']     = $exception->getLine();
                $payload['previous'] = $exception->getPrevious();
                $payload['trace']    = $exception->getTrace();
            }

            $response = new Response();
            $response->getBody()->write(
                json_encode($payload, JSON_UNESCAPED_SLASHES)
            );

            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        };
    }
}
