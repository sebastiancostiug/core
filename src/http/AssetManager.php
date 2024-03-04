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
     * @var array $assets Assets
     */
    protected array $assets = [];

    /**
     * register()
     *
     * @param string $view View or layout name
     * @param string $type Asset type (css or js)
     * @param string $path Asset path
     *
     * @return void
     */
    public function register(string $view, string $type, string $path): void
    {
        $this->assets[$view][$type][] = $path;
    }

    /**
     * getAssets()
     *
     * @param string $view View or layout name
     * @param string $type Asset type (css or js)
     *
     * @return array
     */
    public function getAssets(string $view, string $type): array
    {
        return $this->assets[$view][$type] ?? [];
    }

    /**
     * outputAssets()
     *
     * @param string $view View or layout name
     * @param string $type Asset type (css or js)
     *
     * @return string
     */
    public function outputAssets(string $view, string $type): string
    {
        $assets = $this->getAssets($view, $type);
        $output = '';

        foreach ($assets as $asset) {
            if ($type === 'css') {
                $output .= '<link rel="stylesheet" href="' . $asset . '">';
            } elseif ($type === 'js') {
                $output .= '<script src="' . $asset . '"></script>';
            }
        }

        return $output;
    }
}
