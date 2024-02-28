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
    protected static function access($action): mixed
    {
        return null;
    }
}
