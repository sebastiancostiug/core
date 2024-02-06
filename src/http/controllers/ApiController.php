<?php
/**
 * @package     Core
 *
 * @subpackage  HTTP controllers
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    controllers
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023.10.15
 */

namespace core\http\controllers;

use core\components\Controller;
use Nyholm\Psr7\Response;

/**
 * Controller class
 */
class ApiController extends Controller
{
    /**
     * jsonResponse
     *
     * @param Response $response Nyholm\Psr7\Response
     * @param mixed    $data     Data to be displayed to the user
     * @param integer  $status   HTTP status code
     *
     * @return mixed
     */
    public function jsonResponse(Response $response, mixed $data = null, $status = 200)
    {
        $data = $data ?? ['status' => 'error', 'message' => 'not found'];
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
