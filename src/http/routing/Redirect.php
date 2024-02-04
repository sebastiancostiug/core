<?php
/**
 * @package     Core
 *
 * @subpackage  Redirect class
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    http
 *
 * @since       2024-01-27
 */

namespace core\http\routing;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;

/**
 * Redirect class
 */
class Redirect
{
    /**
     * The response object for handling redirects.
     *
     * @var mixed $response
     */
    protected Response $response;

    /**
     * Class Redirect
     *
     * @param Psr17Factory $factory Response factory
     *
     * @return void
     */
    public function __construct(Psr17Factory $factory)
    {
        $this->response = $factory->createResponse(302, 'Redirect');
    }

    /**
     * __invoke()
     *
     * @param string $url URL to redirect to
     *
     * @return Response
     */
    public function __invoke(string $url): Response
    {
        $this->response = $this->response->withHeader('Location', $url);

        return $this->response;
    }
}
