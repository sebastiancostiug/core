<?php
/**
 * @package     Core
 *
 * @subpackage  Validator
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    components
 *
 * @since       2024-02-01
 */

namespace core\components;

use common\Component;
use common\Translator;
use core\exceptions\ModelException;

/**
 * Validator class
 */
class Validator extends Component
{
    /**
     * @var array $data Holds the data to be validated.
     */
    protected $data = [];

    /**
     * @var array $errors Holds the validation errors.
     */
    protected $errors = [];

    /**
     * The Validator resolver instance.
     *
     * @var \Closure
     */
    protected $resolver;

    /**
     * @var array $rules Holds the validation rules.
     */
    protected $rules = [];

    /**
     * All of the custom validator extensions.
     *
     * @var array<string, \Closure|string>
     */
    protected $extendedRules = [];

    /**
     * @var array $filters Holds the filters to be applied.
     */
    protected $filters = [];

    /**
     * @var array $extnededFilters Holds the extended filters to be applied.
     */
    protected $extendedFilters = [];

    /**
     * Constructor for the Validator class.
     *
     * @param array $data The data to be validated.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;

        $this->rules = [
            'required'  => fn ($field) => empty(trim($this->data[$field] ?? '')),
            'string'    => fn ($field) => !is_string($this->data[$field] ?? ''),
            'integer'   => fn ($field) => !is_numeric($this->data[$field] ?? ''),
            'boolean'   => fn ($field) => !is_bool($this->data[$field] ?? ''),
            'email'     => fn ($field) => !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL),
            'strength'  => fn ($field) => !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', $this->data[$field] ?? ''),
            'lengthMin' => fn ($field, $condition) => strlen($this->data[$field] ?? '') < $condition,
            'lengthMax' => fn ($field, $condition) => strlen($this->data[$field] ?? '') > $condition,
            'match'     => fn ($field, $condition) => !isset($this->data[$field]) || !isset($this->data[$condition]) || $this->data[$field] !== $this->data[$condition],
            'in'        => fn ($field, $condition) => !in_array($this->data[$field], $condition),
            'unique'    => fn ($field, $condition) => (new $condition())->exists($field, $this->data[$field])
        ];

        $this->filters = [
            'trim'      => fn ($field, $condition) => trim($this->data[$field] ?? '', $condition),
            'stripTags' => fn ($field) => strip_tags($this->data[$field]),
            'lowercase' => fn ($field) => strtolower($this->data[$field] ?? ''),
            'hash'      => fn ($field) => password_hash($this->data[$field], PASSWORD_DEFAULT)
        ];
    }

    /**
     * Checks if the data is valid.
     *
     * @return boolean
     * @throws ModelException If the validation gathers errors.
     */
    public function isValid(): bool
    {
        throw_when(!empty($this->errors), ['Model validation failed', $this->errors], ModelException::class);

        return true;
    }

    /**
     * Enforces validation rules on the given data using the specified rules and messages.
     *
     * @param array $rules    The validation rules to be applied.
     * @param array $messages The custom error messages to be used.
     *
     * @return $this The current instance of the Validator.
     */
    public function enforce(array $rules, array $messages = []): Validator
    {
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                $condition = null;
                if (strpos($rule, '=') !== false) {
                    list($rule, $condition) = explode('=', $rule);
                    if (strpos($condition, ',') !== false) {
                        $condition = explode(',', $condition);
                    }
                }

                $message = $messages[$field][$rule] ?? app()->translate('validation', "{$rule}", ['attribute' => $field]);

                try {
                    switch ($rule) {
                        case 'required':
                        case 'email':
                        case 'strength':
                            if ($this->rules[$rule]($field)) {
                                $this->errors[$field] = $message;
                            }
                            break;

                        case 'lengthMin':
                        case 'lengthMax':
                        case 'match':
                        case 'in':
                        case 'unique':
                            if ($this->rules[$rule]($field, $condition)) {
                                $this->errors[$field] = $message;
                            }
                            break;

                        default:
                            if (isset($this->extendedRules[$rule])) {
                                $this->validate($rule, $field, $condition);
                            }
                            break;
                    }
                } catch (\database\DatabaseException $e) {
                    $this->errors[$field] = $e->getMessage();
                }
            }
        }

        return $this;
    }

    /**
     * Filters the given data based on the provided filters.
     *
     * @param array $filters The filters to be applied.
     *
     * @return $this The current instance of the Validator.
     */
    public function filter(array $filters): Validator
    {
        foreach ($filters as $field => $fieldFilters) {
            foreach ($fieldFilters as $filter) {
                if (strpos($filter, ':') !== false) {
                    $filter    = explode(':', $filter);
                    $filter    = $filter[0];
                    $condition = $filter[1];
                }
                switch ($filter) {
                    case 'trim':
                        $this->data[$field] = $this->filters[$filter]($field, $condition);
                        break;
                    case 'stripTags':
                    case 'lowercase':

                    default:
                        if (isset($this->extendedFilters[$filter])) {
                            $this->filter([$filter]);
                        }
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * Adds a custom validator extension.
     *
     * @param string          $rule      The name of the rule.
     * @param \Closure|string $extension The extension to be added.
     *
     * @return void
     */
    public function addRule($rule, $extension)
    {
        $this->extendedRules[$rule] = $extension;
    }

    /**
     * Adds a custom filter extension.
     *
     * @param string          $filter    The name of the filter.
     * @param \Closure|string $extension The extension to be added.
     *
     * @return void
     */
    public function addFilter($filter, $extension)
    {
        $this->extendedFilters[$filter] = $extension;
    }

    /**
     * Calls a custom validator extension.
     *
     * @param string $rule      The name of the rule.
     * @param string $field     The name of the field to be validated.
     * @param string $condition The condition to be applied.
     *
     * @return void
     */
    protected function validate($rule, $field, $condition): void
    {
        $extension = $this->extendedRules[$rule];

        if ($extension instanceof \Closure) {
            $extension($rule, $this->data, $field, $condition);
        } elseif (is_string($extension)) {
            $this->resolve($extension)->validate($rule, $this->data, $field, $condition);
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
