<?php
/**
 * @package     Core
 *
 * @subpackage  Database config
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    config
 *
 * @since       2023.01.23
 */

return [
    'quoteIdentifiers' => true,  // Enable identifier quoting
    'timezone'         => null,  // Set to null to use MySQL servers timezone
    'cacheMetadata'    => false, // Disable meta data cache
    'log'              => false, // Disable query logging
    'persistent'       => false, // Turn off persistent connections

    // PDO options
    'flags'            => [
        PDO::ATTR_PERSISTENT         => false,                  // Turn off persistent connections
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable exceptions
        PDO::ATTR_EMULATE_PREPARES   => true,                   // Emulate prepared statements
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Set default fetch mode to array
        // Convert numeric values to strings when fetching.
        // Since PHP 8.1 integers and floats in result sets will be returned using native PHP types.
        PDO::ATTR_STRINGIFY_FETCHES  => true,                   // This option restores the previous behavior.
    ],
];
