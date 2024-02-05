<?php
/**
 * @package     Core
 *
 * @subpackage  ModelException
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    exceptions
 *
 * @since       2024-02-05
 */

namespace core\exceptions;

/**
 * ModelException class
 */
class ModelException extends \Exception
{
    /**
     * @var array $errors The errors of the model.
     */
    protected array $errors = [];

    /**
     * __construct()
     *
     * @param string         $message  The message of the exception.
     * @param array          $errors   The errors of the exception.
     * @param integer        $code     The code of the exception.
     * @param Throwable|null $previous The previous exception.
     *
     * @return void
     */
    public function __construct(string $message, array $errors = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    /**
     * getErrors()
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Model exception';
    }
}
