<?php
/**
 * @package     slim-base
 *
 * @subpackage  AssetsProvider
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 *
 * @since       2024-02-20
 */

namespace core\providers;

use core\components\ServiceProvider;
use core\http\AssetManager;

/**
 * SessionProvider class
 */
class AssetsProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('assets', function () {
            $assets = new AssetManager();

            return function ($layout) use ($assets) {
                $theme = config('theme.selected');

                foreach (config("theme.$theme.layout.$layout") as $type => $assetsList) {
                    foreach ($assetsList as $asset) {
                        $assets->register($type, $asset);
                    }
                }

                return $assets;
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
