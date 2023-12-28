<?php
/**
 * @package     Slim 4 Base
 *
 * @subpackage  Core - kernel
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

namespace seb\http;

use seb\bootstrap\foundation\Kernel;
use seb\bootstrap\foundation\bootstrappers\Debug;
use seb\bootstrap\foundation\bootstrappers\EnvironmentDetector;
use seb\bootstrap\foundation\bootstrappers\EnvironmentVariables;
use seb\bootstrap\foundation\bootstrappers\Middleware;
use seb\bootstrap\foundation\bootstrappers\ServiceProviders;

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
