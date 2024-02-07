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
class TranslatorProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('translate', function () {
            return function ($translations, $key, $replacements = [], $locale = null) {
                $loader = app()->resolve(Fileloader::class);
                $loader->addNamespace('language', config('translate.path'));
                $loader->load(config('app.locale'), $translations, 'language');

                $translator = new Translator($loader, $translations, config('app.locale'));

                return $translator->translate($key, $replacements, $locale);
            };
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
