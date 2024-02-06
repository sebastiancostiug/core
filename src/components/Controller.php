<?php
/**
 * @package     slim-base
 *
 * @subpackage  Controller
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
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
     * __construct
     *
     * @param array $config Configuration
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->init();
    }

    /**
     * init
     *
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * beforeAction
     *
     * @param string $action Action
     *
     * @return void
     */
    public function beforeAction(string $action): void
    {
    }

    /**
     * afterAction
     *
     * @param string $action Action
     *
     * @return void
     */
    public function afterAction(string $action): void
    {
    }
}
