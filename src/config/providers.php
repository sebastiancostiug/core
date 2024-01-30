<?php
/**
 * @package     Core
 *
 * @subpackage  Service Providers config
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    config
 *
 * @since       2023.01.23
 */

use core\providers\ConsoleProvider;
use core\providers\FilesystemProvider;
use core\providers\RoutesProvider;
use core\providers\ViewProvider;

return [
    'web' => [
        FilesystemProvider::class,
        RoutesProvider::class,
        ViewProvider::class,
    ],
    'console' => [
        FilesystemProvider::class,
        ConsoleProvider::class,
    ],
];
