<?php
/**
 * @package     Core
 *
 * @subpackage  Controller
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    components
 *
 * @since       2024-02-06
 */

namespace core\components;

use common\Component;

/**
 * Controller class
 */
class Controller extends Component
{
    /**
     * returns the access level for the action
     *
     * @param string $action The action
     *
     * @return mixed null for public access, integer for specific role access, array for multiple role access
     */
    public static function access($action): mixed
    {
        return null;
    }

    /**
     * Provides data for a given class and input.
     *
     * @param string  $class    The class name.
     * @param array   $filter   The filter data.
     * @param integer $pageSize The page size (default: 20).
     * @param integer $page     The page number (default: 1).
     * @param string  $orderBy  The order by field (default: 'created_at').
     * @param string  $order    The order (default: 'DESC').
     *
     * @return array
     */
    protected function provide($class, array $filter, $pageSize = 20, $page = 1, $orderBy = 'created_at', $order = 'DESC')
    {
        if ($pageSize < 1) {
            $pageSize = 20;
        }

        if ($page < 1) {
            $page = 1;
        }

        $filter       = $filter ?? [];
        $offset       = ($page - 1) * $pageSize;
        $totalEntries = $class::count();
        $pages        = ceil($totalEntries / $pageSize);

        if ($page > 1 && $page > $pages) {
            $offset = ($pages - 1) * $pageSize;
        }

        $records = $class::getEntries($filter, $offset, $pageSize, $orderBy, $order);

        return [
            'entries'     => $records,
            'pages'       => $pages,
            'currentPage' => $page,
        ];
    }
}
