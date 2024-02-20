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
            /*
             * Returns a closure that creates a new Validator instance and applies filters and rules to it.
             *
             * @param mixed $data The data to be validated.
             * @param array $rules The validation rules to be enforced.
             * @param array $filters The filters to be applied to the data before validation (optional).
             *
             * @return Validator The Validator instance with filters and rules applied.
             */

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
