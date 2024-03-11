<?php
/**
 * @package     Database
 *
 * @subpackage  Model
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    database
 *
 * @since       2023-10-30
 */

namespace core\components;

use core\exceptions\ModelException;
use database\Record;
use database\RecordInterface;

/**
 * Model class
 *
 * Provides several attribute methods to handle the model's data:
 * - attributeNames - returns an array of attribute names for the model that are the keys of the labels array and are used as class properties.
 * - getAttributes - returns the model's attributes that correspond to the attributeNames and are used as class properties.
 * - setAttributes - sets the model's attributes that correspond to the attributeNames and are used as class properties.
 * - setPrivateAttributes - sets the private attributes of the model that are not accessible from outside the model.
 * - getPublicAttributes - returns the public attributes of the model that are accessible from outside the model.
 * - setOldAttributes - sets the old attributes of the model to be able to get the changedAttributes.
 * - getChangedAttributes - returns the changed attributes of the model
 * - setChangedAttributes - sets the changed attributes of the model
 * - hasAttribute - checks if the model has an attribute with the specified name
 *
 * Provides several methods to interact with the database using the Record class:
 * - find - returns a new instance of the Record class to perform database operations.
 * - findBy - finds a record by a specific field and value.
 * - findAllBy - finds all records by a specific field and value.
 * - findOne - returns a single model instance.
 * - findAll - returns all records of the model.
 * - exists - checks if a record exists in the database based on a column and value.
 * - save - saves the record to the database.
 * - delete - deletes the record from the database.
 * - hasOne - returns a single model instance related to the model.
 * - hasMany - returns all related records of the model.
 *
 * {@inheritDoc}
 */
class Model extends Eventful implements RecordInterface
{
    /**
     * Class events
     */
    const EVENT_AFTER_FIND      = 'afterFind';
    const EVENT_BEFORE_VALIDATE = 'beforeValidate';
    const EVENT_AFTER_VALIDATE  = 'afterValidate';
    const EVENT_BEFORE_INSERT   = 'beforeInsert';
    const EVENT_AFTER_INSERT    = 'afterInsert';
    const EVENT_BEFORE_UPDATE   = 'beforeUpdate';
    const EVENT_AFTER_UPDATE    = 'afterUpdate';
    const EVENT_BEFORE_DELETE   = 'beforeDelete';
    const EVENT_AFTER_DELETE    = 'afterDelete';

    /**
     * The default scenario for the model.
     */
    const SCENARIO_DEFAULT = 'default';

    /**
     * @var string $scenario The scenario to be used for validation.
     */
    protected string $scenario = '';

    /**
     * @var array $labels Labels
     */
    protected array $labels = [
        'id'         => 'ID',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
    ];

    /**
     * @var array $defaults Defaults
     */
    protected array $defaults = [];

    /**
     * @var array $rules Rules
     */
    protected array $rules = [
        'created_by' => ['integer'],
        'updated_by' => ['integer'],
    ];

    /**
     * @var array $filters Filters
     */
    protected array $filters = [
            'id'         => ['integer'],
            'created_by' => ['integer'],
            'created_at' => ['datetime'],
            'updated_by' => ['integer'],
            'updated_at' => ['datetime'],
        ];

    /**
     * The array to store any validation errors for the model.
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * @var array attribute values indexed by attribute names
     */
    private $_attributes = [];

    /**
     * @var array $_privateAttributes The private attributes of the model.
     */
    private array $_privateAttributes = [];

    /**
     * The array that stores the old attribute values of the model.
     *
     * @var array
     */
    protected array $oldAttributes = [];

    /**
     * The array that holds the changed attributes of the model.
     *
     * @var array
     */
    protected array $changedAttributes = [];

    /**
     * Constructor for the Model class.
     *
     * @param array $attributes The attributes to be set.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->setLabels($this->labels());
        $this->setRules($this->rules());
        $this->setFilters($this->filters());
        $this->setDefaults($this->defaults());
        $this->setPrivateAttributes($this->private());

        $this->setAttributes($attributes);

        $this->init();
    }

    /**
     * Initializes the model.
     *
     * This method is called at the end of the constructor.
     * You may override this method to do some initialization when your model is created.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Magic method to get a property value by calling its corresponding getter method.
     *
     * @param string $name The name of the property to get.
     *
     * @return mixed The value of the property. Null if the property does not exist or is write-only.
     */
    public function __get($name): mixed
    {
        if (in_array($name, $this->attributeNames())) {
            return $this->_attributes[$name] ?? null;
        }
        return parent::__get($name);
    }

    /**
     * Sets the value of a component property.
     *
     * This method will check in the following order and act accordingly:
     *
     *  - a property defined by a setter: set the property value
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$component->property = $value;`.
     *
     * @param string $name  The property name or the event name
     * @param mixed  $value The property value
     *
     * @return void
     */
    public function __set($name, mixed $value)
    {
        if (in_array($name, $this->attributeNames())) {
            $this->_attributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * Checks if a property is set.
     *
     * This magic method is called when using the `isset()` function on an inaccessible property of the object.
     *
     * @param string $name The name of the property to check.
     * @return boolean Returns `true` if the property is set, `false` otherwise.
     */
    public function __isset($name): bool
    {
        if (in_array($name, $this->attributeNames())) {
            return isset($this->_attributes[$name]);
        }
        return parent::__isset($name);
    }

    /**
     * Get the model's table name
     *
     * @return string
     */
    public function tableName()
    {
        return $this->table;
    }

    /**
     * tableExists
     *
     * @return boolean
     */
    public function tableExists()
    {
        $record = new Record(static::class);

        return $record->tableExists();
    }

    /**
     * afterFind
     *
     * @return $this
     */
    public function afterFind()
    {
        $this->setOldAttributes();
        $this->notify(self::EVENT_AFTER_FIND);

        return $this;
    }

    /**
     * beforeValidate
     *
     * @return void
     */
    public function beforeValidate()
    {
        if ($this->isNewRecord()) {
            foreach ($this->defaults as $key => $value) {
                $assignedValue = $this->$key;
                if (!$assignedValue) {
                    $this->$key = $value;
                }
            }
        }

        $this->notify(self::EVENT_BEFORE_VALIDATE);
    }

    /**
     * validate
     *
     * @return boolean
     */
    public function validate()
    {
        $this->beforeValidate();

        if ($this->scenario) {
            $rules   = $this->rules[$this->scenario] ?? [];
            $filters = $this->filters[$this->scenario] ?? [];
        } else {
            $rules   = $this->rules;
            $filters = $this->filters;
        }

        try {
            app()->validate($this->attributes, $rules, $filters)->isValid();
            $this->notify(self::EVENT_AFTER_VALIDATE);

            return true;
        } catch (ModelException $e) {
            $this->setErrors($e->getErrors());

            return false;
        }
    }

    /**
     * This method is called before inserting a record.
     * Override this method to add custom logic before saving.
     *
     * @return void
     */
    public function beforeSave()
    {
        if ($this->isNewRecord()) {
            $this->notify(self::EVENT_BEFORE_INSERT);
        } else {
            $this->setChangedAttributes();
            $this->notify(self::EVENT_BEFORE_UPDATE);
        }
    }

    /**
     * This method is called after inserting a record.
     * Override this method to add custom logic after saving.
     *
     * @return void
     */
    public function afterSave()
    {
        if ($this->isNewRecord()) {
            $this->notify(self::EVENT_AFTER_INSERT);
        } else {
            $this->notify(self::EVENT_AFTER_UPDATE);
        }
    }

    /**
     * beforeDelete
     *
     * @return void
     */
    public function beforeDelete()
    {
        $this->notify(self::EVENT_BEFORE_DELETE);
    }

    /**
     * afterDelete
     *
     * @return void
     */
    public function afterDelete()
    {
        $this->notify(self::EVENT_AFTER_DELETE);
    }

    /**
     * Find a record by conditions
     *
     * @return Record The model instance if found, or false if not found.
     */
    public static function find(): Record
    {
        $record = new Record(static::class);

        return $record->find();
    }

    /**
     * Find a record by a specific field and value
     *
     * @param  array $conditions Conditions
     *
     * @return self|false The model instance if found, or false if not found.
     */
    public static function findBy(array $conditions): self|false
    {
        $record = static::find()->where($conditions)->one();

        if (empty($record)) {
            return false;
        }

        $instance = new static();

        return $instance->load($record)->afterFind();
    }

        /**
     * Find all records by a specific field and value
     *
     * @param  array $conditions Conditions
     *
     * @return array|false An array with all records of the model or false if no records are found.
     */
    public static function findAllBy(array $conditions): array|false
    {
        $fetchData = static::find()->where($conditions)->all();

        if (empty($fetchData)) {
            return false;
        }

        $results = [];
        foreach ($fetchData as $data) {
            $instance = new static();
            $model = $instance->load($data)->afterFind();
            $results[] = $model;
        }

        return $results;
    }

    /**
     * Returns a single model instance
     *
     * @param  integer $id ID
     *
     * @return self|false The model instance if found, or false if not found.
     */
    public static function findOne($id): self|false
    {
        $record = static::find()->where(['id' => $id])->one();

        if (empty($record)) {
            return false;
        }
        $instance = new static();

        return $instance->load($record)->afterFind();
    }

    /**
     * Get all records of the model
     *
     * @return array|false An array with all records of the model or false if no records are found.
     */
    public static function findAll(): array|false
    {
        $fetchData = static::find()->all();

        if (empty($fetchData)) {
            return false;
        }

        $results = [];
        foreach ($fetchData as $data) {
            $instance = new static();
            $model = $instance->load($data)->afterFind();
            $results[] = $model;
        }

        return $results;
    }

    /**
     * Retrieves JUST the attributes saved in the database for all the models.
     *
     * @param array   $filter  The filter to apply to the records.
     * @param integer $offset  The offset to start from.
     * @param integer $count   The number of records to retrieve.
     * @param string  $orderBy The column to order by.
     * @param string  $sort    The order to use.
     *
     * @return array An array containing all the entries.
     */
    public static function getEntries(array $filter = [], $offset = null, $count = null, $orderBy = 'id', $sort = 'ASC')
    {
        $records = new Record(static::class);

        $records = $records->find();

        if (!empty($filter)) {
            $records = $records->where($filter);
        }

        if ($offset) {
            $records = $records->offset($offset);
        }

        $records = $records->orderBy($orderBy, $sort);

        if ($count) {
            $records = $records->limit($count);
        }

        return $records->all();
    }

    /**
     * Check if a record exists in the database based on a column and value.
     *
     * @param string $column The column to search in.
     * @param mixed  $value  The value to search for.
     *
     * @return boolean Returns true if a record exists, false otherwise.
     */
    public function exists($column, mixed $value)
    {
        $record = new Record(static::class);

        return (bool) $record->find()->where([$column => $value])->one();
    }

    /**
     * count the records
     *
     * @return integer
     */
    public static function count()
    {
        $record = new Record(static::class);

        return $record->count();
    }

    /**
     * Save the record
     *
     * @return integer|boolean The ID of the inserted record or boolean on update and failure.
     */
    public function save(): int|bool
    {
        $record = new Record(static::class);

        if (!$this->validate()) {
            return false;
        }
        $this->beforeSave();

        if ($this->isNewRecord()) {
            $attributes = array_filter($this->getAttributes(), function ($value) {
                return !empty($value);
            });
        } else {
            $attributes = $this->getChangedAttributes() + ['id' => $this->id];
        }

        if (!empty($attributes)) {
            $result = $record->setAttributes($attributes)->save();

            $this->afterSave();
        }

        return $result ?? $this->id ?? true;
    }

    /**
     * Delete the record
     *
     * @return boolean True if the record is successfully deleted, false otherwise.
     */
    public function delete(): bool
    {
        $this->beforeDelete();

        $record = new Record(static::class);
        $record->setAttributes($this->getAttributes());

        if (!$record->delete()) {
            return false;
        }

        $this->afterDelete();

        return true;
    }

    /**
     * Get a record relation using hasOne relationship
     *
     * @param  string      $class    Class
     * @param  string|null $viaTable Intermediate table
     *
     * @return object|false The related record if found, or false if not found.
     */
    public function hasOne($class, $viaTable = null): object|false
    {
        $record = new Record(static::class);
        $record->has($class);

        if ($viaTable) {
            $record = $record->via($viaTable);
        }

        return $record->one();
    }

    /**
     * Get record relation using hasMany relationship
     *
     * @param  string      $class    Class
     * @param  string|null $viaTable Intermediate table
     *
     * @return array|false An array with all related records, or false if no records are found.
     */
    public function hasMany($class, $viaTable = null): array|false
    {
        $record = new Record(static::class);
        $record->has($class);

        if ($viaTable) {
            $record = $record->via($viaTable);
        }

        return $record->all();
    }

    /**
     * Returns an array of attribute names for the model.
     *
     * @return array The attribute names.
     */
    public function attributeNames()
    {
        return array_keys($this->getLabels());
    }

    /**
     * getAttributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        $attributes = [];

        foreach ($this->attributeNames() as $key) {
            $attributes[$key] = $this->$key;
        }

        return $attributes;
    }

    /**
     * setAttributes
     *
     * @param array $values Attribute values
     *
     * @return void
     */
    public function setAttributes(array $values): void
    {
        $attributes = $this->attributeNames();

        foreach ($values as $name => $value) {
            if (in_array($name, $attributes) && isset($value)) {
                $this->$name = $value;
            }
        }
    }

    /**
     * Set the private attributes of the model.
     *
     * @param array $attributes The attributes to set.
     *
     * @return void
     */
    public function setPrivateAttributes(array $attributes): void
    {
        $this->_privateAttributes = $attributes;
    }

    /**
     * Get the public attributes of the model.
     *
     * @return array
     */
    public function getPublicAttributes()
    {
        $attributes = $this->getAttributes();

        foreach ($this->_privateAttributes as $attribute) {
            unset($attributes[$attribute]);
        }

        return $attributes;
    }

    /**
     * Set the old attributes of the model
     *
     * @return void
     */
    public function setOldAttributes()
    {
        $this->oldAttributes = !$this->isNewRecord() ? $this->getAttributes() : [];
    }

    /**
     * Returns the attributes that have been changed in the model.
     *
     * @return array The array of changed attributes.
     */
    public function getChangedAttributes()
    {
        return $this->changedAttributes;
    }

    /**
     * Set the changed attributes of the model
     *
     * @return void
     */
    public function setChangedAttributes()
    {
        $this->changedAttributes = array_diff_assoc($this->getAttributes(), $this->oldAttributes);
    }

    /**
     * Check if the model has an attribute with the specified name
     *
     * @param string $name The name of the attribute
     *
     * @return boolean Whether the model has an attribute with the specified name.
     */
    public function hasAttribute($name)
    {
        return property_exists($this, $name);
    }

    /**
     * Get the default values for the model.
     *
     * @return array The default values.
     */
    public function defaults(): array
    {
        return [];
    }

    /**
     * Returns an array of private data.
     *
     * @return array The private data.
     */
    public function private(): array
    {
        return [];
    }

    /**
     * Get the validation rules for the model.
     *
     * @return array The validation rules.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Get the filters for the model.
     *
     * @return array The filters for the model.
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Get the default values for the model.
     *
     * @return array The default values.
     */
    public function labels(): array
    {
        return [];
    }

    /**
     * Get the default values for the model.
     *
     * @return array The default values.
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Set the default values for the model.
     *
     * @param array $defaults The default values to set.
     * @return void
     */
    public function setDefaults(array $defaults): void
    {
        $this->add('defaults', $defaults);
    }

    /**
     * Get the validation rules for the model.
     *
     * @return array The validation rules.
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Set the validation rules for the model.
     *
     * @param array $rules The validation rules to set.
     * @return void
     */
    public function setRules(array $rules): void
    {
        $this->add('rules', $rules);
    }

    /**
     * Get the filters for the model.
     *
     * @return array The filters for the model.
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Set the filters for the model.
     *
     * @param array $filters The filters to be set.
     * @return void
     */
    public function setFilters(array $filters): void
    {
        $this->add('filters', $filters);
    }

    /**
     * Get the validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Set the validation errors
     *
     * @param array $errors The validation errors to set.
     *
     * @return void
     */
    public function setErrors(array $errors)
    {
        app()->session()->flash('errors', $errors);
        $this->errors = $errors;
    }

    /**
     * Get the labels associated with the model.
     *
     * @return array The labels associated with the model.
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Set the labels for the model.
     *
     * @param array $labels The labels to be set.
     * @return void
     */
    public function setLabels(array $labels): void
    {
        $this->add('labels', $labels);
    }

    /**
     * Get the scenario for the model.
     *
     * @return string|null The scenario for the model.
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * Set the scenario for the model.
     *
     * @param string $scenario The scenario to set.
     * @return void
     */
    public function setScenario($scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * Load data into a new instance of the model.
     *
     * @param array  $data  The data to load into the model.
     * @param string $scope The scope of the data to load.
     *
     * @return self The new instance of the model with the loaded data.
     */
    public function load(array $data, string $scope = null): self
    {
        $model = new static();
        if ($scope) {
            $model->setAttributes($data[$scope]);
        } else {
            $model->setAttributes($data);
        }

        return $model;
    }

    /**
     * Get the validation errors
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Check if the model is a new record
     *
     * @return boolean
     */
    public function isNewRecord()
    {
        return empty($this->id);
    }

    /**
     * Get the last inserted ID
     *
     * @return integer
     */
    public function lastInsertId()
    {
        $record = new Record(static::class);
        return $record->lastInsertId();
    }

    /**
     * Add values to an array attribute
     *
     * @param string $array  The array attribute to add to
     * @param array  $values The values to add
     *
     * @return void
     */
    public function add($array, array $values)
    {
        if (!isset($this->$array)) {
            $this->$array = [];
        }

        $this->$array = array_merge($this->$array, $values);
    }

    /**
     * Add an error to the model
     *
     * @param string $field   The field to add the error to
     * @param string $message The error message
     *
     * @return void
     */
    public function addError($field, $message)
    {
        $this->add('errors', [$field => $message]);
    }

    /**
     * Check if the model has a specific attribute.
     *
     * @param string $attribute The attribute to check.
     *
     * @return boolean Returns true if the attribute exists, false otherwise.
     */
    public function has($attribute)
    {
        return property_exists($this, $attribute);
    }
}
