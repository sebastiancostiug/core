<?php
/**
 * @package     slim-api-skeleton
 *
 * @subpackage  AssetManager
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-api-skeleton
 *
 * @since       2024-03-04
 */

namespace core\http;

use core\components\Component;

/**
 * AssetManager class
 */
class AssetManager extends Component
{
    /**
     * @var array $css CSS assets
     */
    protected array $css = [];

    /**
     * @var array $js JS assets
     */
    protected array $js = [];

    /**
     * @var array $icon Icon assets
     */
    protected array $icon = [];

    /**
     * register()
     *
     * @param string $type Asset type (css or js or icon)
     * @param string $path Asset path
     *
     * @return void
     */
    public function register(string $type, string $path): void
    {
        if ($type === 'css') {
            $this->css[] = $path;
        } elseif ($type === 'js') {
            $this->js[] = $path;
        } elseif ($type === 'icon') {
            $this->icon[] = $path;
        }
    }

    /**
     * get()
     *
     * @param string $type Asset type (css or js or icon)
     *
     * @return array
     */
    protected function get(string $type): array
    {
        if ($type === 'css') {
            return $this->css;
        } elseif ($type === 'js') {
            return $this->js;
        } elseif ($type === 'icon') {
            return $this->icon;
        }

        return [];
    }

    /**
     * outputAssets()
     *
     * @param string $type Asset type (css or js or icon)
     *
     * @return string
     */
    public function output(string $type): string
    {
        $assets = $this->get($type);
        $output = '';
        foreach ($assets as $asset) {
            $assetsPath = assets_path(config('theme.selected'));
            $assetUrl = str_starts_with($asset, 'http') ? $asset : "/assets.php?file={$assetsPath}" . $asset;

            if ($type === 'css') {
                $output .= '<link rel="stylesheet" href="' . $assetUrl . '"/>' . PHP_EOL;
            } elseif ($type === 'js') {
                $output .= '<script src="' . $assetUrl . '"></script>' . PHP_EOL;
            } elseif ($type === 'icon') {
                $output .= '<link rel="icon" href="' . $assetUrl . '" type="image/x-icon"/>' . PHP_EOL;
            }
        }

        return $output;
    }
}
