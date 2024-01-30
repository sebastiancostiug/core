<?php
/**
 * @package     Core
 *
 * @subpackage  Service Provider class
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    Service Providers
 *
 * @since       2024.01.30
 */

namespace core\providers;

use core\components\Filesystem;
use core\components\ServiceProvider;

/**
 * Service Provider class
 */
class FilesystemProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        $this->bind('Filesystem', fn () => new Filesystem);
    }

    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
    }
}
