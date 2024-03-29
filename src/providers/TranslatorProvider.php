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
            /*
             * Returns a closure that translates a given key using the specified translations, replacements, and locale.
             *
             * @param array $translations The translations array.
             * @param string $key The key to be translated.
             * @param array $replacements The replacements array for placeholders in the translation.
             * @param string|null $locale The locale to be used for translation. If null, the default locale will be used.
             *
             * @return string The translated string.
             */

            return function ($translations, $key, $replacements = [], $locale = null) {
                $loader = app()->resolve(Fileloader::class);
                $loader->addNamespace('language', config('translate.path'));
                $loader->load(env('APP_LOCALE', 'en'), $translations, 'language');

                $translator = new Translator($loader, $translations, env('APP_LOCALE', 'en'));

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
