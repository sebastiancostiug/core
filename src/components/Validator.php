<?php
/**
 *
 * @package     slim-base
 *
 * @subpackage  Validator
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 * @see
 *
 * @since       2024-02-01
 *
 */

namespace core\components;

/**
 * Validator class
 */
class Validator
{
    /**
     * @var array $errors Holds the validation errors.
     */
    protected $errors;

    /**
     * Constructor for the Validator class.
     *
     * @param array $errors An array of errors.
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Check if the validation fails.
     *
     * @return boolean Returns true if the validation fails, false otherwise.
     */
    public function fails()
    {
        return !empty($this->errors);
    }

    /**
     * Get the errors from the validator.
     *
     * @return array The array of errors.
     */
    public function errors()
    {
        return $this->errors;
    }
}
