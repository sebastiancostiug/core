<?php
/**
 * @package     Slim 4 Base
 *
 * @subpackage  Config
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    config
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023.01.23
 */

use core\providers\ConsoleProvider;
use core\providers\ViewProvider;

return [
    'web' => [
        ViewProvider::class,
    ],
    'console' => [
        ConsoleProvider::class,
    ],
];
