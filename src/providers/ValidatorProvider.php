<?php
/**
 * @package     Core
 *
 * @subpackage  ValidatorProvider
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    Service Providers
 *
 * @since       2024-02-04
 */

namespace core\providers;

use common\Translator;
use core\components\ServiceProvider;
use core\components\Validator;

/**
 * ValidatorProvider class
 */
class ValidatorProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('validate', function () {
            return function ($data, $rules, $filters = []) {
                $validator = new Validator($data);

                if (!empty($filters)) {
                    $validator = $validator->filter($filters);
                }

                $validator = $validator->enforce($rules);

                return $validator;
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
{

}
