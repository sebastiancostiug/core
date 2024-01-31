<?php
/**
 * @package     Slim 4 Base
 *
 * @subpackage  Config
 *
 * @author      Sebastian Costiug, sebastian@overbyte.dev
 * @copyright   2019-2023 Sebastian Costiug <https://www.overbyte.dev>
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    config
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023.10.14
 */

return [
    'Filesystem' => common\Filesystem::class,
    'Collection' => common\Collection::class,
    'CsrfEcho'   => core\http\middleware\CsrfEcho::class,
];
