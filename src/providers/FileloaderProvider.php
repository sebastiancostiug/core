<?php
/**
 * @package     Core
 *
 * @subpackage  Translator Service Provider class
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    Service Providers
 *
 * @since       2024-02-01
 */

namespace core\providers;

use common\Fileloader;
use common\Filesystem;
use common\Translator;
use core\components\ServiceProvider;

/**
 * TranslatorProvider class
 */
class FileloaderProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Fileloader::class, fn(Filesystem $files, $path) => new Fileloader($files, $path));
    }

    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
        //  placeholder
    }
}
