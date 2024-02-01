<?php
/**
 *
 * @package     slim-api-skeleton
 *
 * @subpackage  TranslatorProvider
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-api-skeleton
 * @see
 *
 * @since       2024-02-01
 *
 */

namespace core\providers;

use common\Fileloader;
use common\Filesystem;
use common\Translator;
use core\components\ServiceProvider;

/**
 * TranslatorProvider class
 */
class TranslatorProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Fileloader::class, fn(Filesystem $files) => new Fileloader($files, config('translate.path')));

        $this->app->bind(Translator::class, function (Fileloader $loader) {
            $loader->addNamespace('language', config('translate.path'));
            $loader->load(config('app.locale'), 'validation', 'language');

            return new Translator($loader, config('app.locale'));
        });
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
