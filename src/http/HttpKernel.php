<?php
/**
 * @package     Core
 *
 * @subpackage  Http Kernel
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    kernel
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023-10-17
 */

namespace core\http;

use core\foundation\Kernel;
use core\bootstrap\Debug;
use core\bootstrap\EnvironmentDetector;
use core\bootstrap\EnvironmentVariables;
use core\bootstrap\Middleware;
use core\bootstrap\ServiceProviders;

/**
 * HttpKernel class
 */
class HttpKernel extends Kernel
{
    /**
     * @var array $middleware Global middleware
     */
    public array $middleware = [];

    /**
     * @var array $middlewareGroups Route group middleware
     */
    public array $middlewareGroups = [
        'api' => [],
        'web' => [],
    ];

    /**
     * @var array $boostrap Register application bootstrap loaders
     */
    public array $bootstrappers = [
        EnvironmentDetector::class,
        EnvironmentVariables::class,
        Debug::class,
        Middleware::class,
        ServiceProviders::class,
    ];
}
