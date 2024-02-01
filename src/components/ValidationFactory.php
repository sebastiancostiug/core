<?php
/**
 *
 * @package     Slim 4 Base
 *
 * @subpackage  Validation
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    factory
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2022.11.10
 *
 */

namespace core\components;

use common\Translator;

/**
 * Class ValidationFactory
 *
 * This class represents a factory for creating validation objects.
 */
class ValidationFactory
{
    /**
     * The Translator implementation.
     *
     * @var Translator
     */
    protected $translator;

    /**
     * All of the custom validator extensions.
     *
     * @var array<string, \Closure|string>
     */
    protected $extensions = [];

    /**
     * All of the custom validator message replacers.
     *
     * @var array<string, \Closure|string>
     */
    protected $replacers = [];

    /**
     * All of the fallback messages for custom rules.
     *
     * @var array<string, string>
     */
    protected $fallbackMessages = [];

    /**
     * The Validator resolver instance.
     *
     * @var \Closure
     */
    protected $resolver;

    /**
     * Create a new Validator factory instance.
     *
     * @param Translator $translator The Translator implementation.
     * @return void
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Enforces validation rules on the given data using the specified rules and messages.
     *
     * @param array $data     The data to be validated.
     * @param array $rules    The validation rules to be applied.
     * @param array $messages The custom error messages to be used.
     *
     * @return Validator The Validator instance.
     */
    public function enforce(array $data, array $rules, array $messages = []): Validator
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $rules = explode('|', $rule);

            foreach ($rules as $rule) {
                $condition = null;
                if (strpos($rule, ':') !== false) {
                    list($rule, $condition) = explode(':', $rule);
                }

                $message = $messages[$field][$rule] ?? $this->translator->translate("validation.{$rule}", ['attribute' => $field]);

                switch ($rule) {
                    case 'required':
                        if (!isset($data[$field]) || $data[$field] === '') {
                            $errors[$field][] = $message;
                        }
                        break;

                    case 'email':
                        if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = $message;
                        }
                        break;

                    case 'strength':
                        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', $data[$field] ?? '')) {
                            $errors[$field][] = $message;
                        }
                        break;

                    case 'lengthMin':
                        if (strlen($data[$field]) < $condition) {
                            $errors[$field][] = $message;
                        }
                        break;

                    case 'lengthMax':
                        if (strlen($data[$field]) > $condition) {
                            $errors[$field][] = $message;
                        }
                        break;

                    case 'match':
                        if ($data[$field] !== $data[$condition]) {
                            $errors[$field][] = $message;
                        }
                        break;

                    case 'in':
                        if (!in_array($data[$field], explode(',', $condition))) {
                            $errors[$field][] = $message;
                        }
                        break;

                    case 'unique':
                        $model = new $condition();
                        if ($model->exists($field, $data[$field])) {
                            $errors[$field][] = $message;
                        }
                        break;

                    default:
                        if (isset($this->extensions[$rule])) {
                            $this->validate($rule, $data, $field, $condition);
                        } else {
                            throw new \Exception("The validation rule '{$rule}' does not exist.");
                        }
                        break;
                }
            }
        }

        return new Validator($errors);
    }

    /**
     * Filters the given data based on the provided filters.
     *
     * @param array $data    The data to be filtered.
     * @param array $filters The filters to be applied.
     *
     * @return array The filtered data.
     */
    public function filter(array $data, array $filters): array
    {
        foreach ($filters as $field => $filter) {
            $filters = explode('|', $filter);

            foreach ($filters as $filter) {
                if (strpos($filter, ':') !== false) {
                    $filter    = explode(':', $filter);
                    $filter    = $filter[0];
                    $condition = $filter[1];
                }
                switch ($filter) {
                    case 'trim':
                        $data[$field] = trim($data[$field]);
                        break;

                    case 'stripTags':
                        $data[$field] = strip_tags($data[$field]);
                        break;

                    case 'lowercase':
                        $data[$field] = strtolower($data[$field]);
                        break;

                    case 'hash':
                        $data[$field] = password_hash($data[$field], PASSWORD_DEFAULT);
                        break;
                }
            }
        }

        return $data;
    }

    /**
     * Adds a custom validator extension.
     *
     * @param string          $rule      The name of the rule.
     * @param \Closure|string $extension The extension to be added.
     *
     * @return void
     */
    public function extend($rule, $extension)
    {
        $this->extensions[$rule] = $extension;
    }

    /**
     * Calls a custom validator extension.
     *
     * @param string $rule      The name of the rule.
     * @param array  $data      The data to be validated.
     * @param string $field     The name of the field to be validated.
     * @param string $condition The condition to be applied.
     *
     * @return void
     */
    protected function validate($rule, array $data, $field, $condition)
    {
        $extension = $this->extensions[$rule];

        if ($extension instanceof \Closure) {
            $extension($rule, $data, $field, $condition);
        } elseif (is_string($extension)) {
            $this->resolve($extension)->validate($rule, $data, $field, $condition);
        }
    }

    /**
     * Resolves a validator extension.
     *
     * @param string $extension The extension to be resolved.
     *
     * @return mixed The resolved extension.
     */
    protected function resolve($extension)
    {
        return call_user_func($this->resolver, $extension);
    }

    /**
     * Sets the Validator resolver instance.
     *
     * @param \Closure $resolver The Validator resolver instance.
     *
     * @return void
     */
    public function setResolver(\Closure $resolver)
    {
        $this->resolver = $resolver;
    }
}
