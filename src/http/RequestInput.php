<?php
/**
 *
 * @package     slim-base
 *
 * @subpackage  RequestInput
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 * @see
 *
 * @since       2024-01-27
 *
 */

namespace core\http;

use Nyholm\Psr7\ServerRequest as Request;
use Slim\Interfaces\RouteInterface;

/**
 * RequestInput class
 */
class RequestInput
{
    /**
     * @var array $metadata
     * The metadata associated with the request input.
     */
    protected array $metadata;
    /**
     * @var array $attributes The attributes of the request input.
     */
    protected array $attributes;

    /**
     * __construct()
     *
     * @param Request        $request Request
     * @param RouteInterface $route   Route
     */
    public function __construct(Request $request, RouteInterface $route)
    {
        $this->metadata = [
            'name'      => $route->getName(),
            'groups'    => $route->getGroups(),
            'methods'   => $route->getMethods(),
            'arguments' => $route->getArguments(),
            'uri'       => $request->getUri(),
        ];

        $this->attributes = $request->getParsedBody() ?? [];
    }

    /**
     * Get all the attributes of the request input.
     *
     * @return array The attributes of the request input.
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Magic method to get the value of a property.
     *
     * @param string $property The name of the property to get.
     * @return mixed|null The value of the property if it exists, otherwise null.
     */
    public function __get($property)
    {
        return $this->attributes[$property] ?? null;
    }

    /**
     * Sets the value of a property.
     *
     * @param string $property The name of the property.
     * @param mixed  $value    The value to set.
     * @return void
     */
    public function __set($property, mixed $value)
    {
        $this->attributes[$property] = $value;
    }

    /**
     * Removes the specified property from the request input attributes.
     *
     * @param string $property The name of the property to remove.
     * @return $this The current instance of the RequestInput object.
     */
    public function forget($property)
    {
        if (isset($this->attributes[$property])) {
            unset($this->attributes[$property]);
        }

        return $this;
    }

    /**
     * Check if the request input has a specific property.
     *
     * @param string $property The name of the property to check.
     * @return boolean Returns true if the property exists, false otherwise.
     */
    public function has($property)
    {
        return isset($this->attributes[$property]);
    }

    /**
     * Merges the given array into the request input attributes.
     *
     * @param array $array The array to merge.
     * @return $this
     */
    public function merge(array $array)
    {
        array_walk($array, function ($value, $key) {
            data_set($this->attributes, $key, $value);
        });

        return $this;
    }

    /**
     * Fills the request input with the given array of data.
     *
     * @param array $array The array of data to fill the request input with.
     *
     * @return $this The updated RequestInput object.
     */
    public function fill(array $array)
    {
        array_walk($array, function ($value, $key) {
            data_fill($this->attributes, $key, $value);
        });

        return $this;
    }

    /**
     * Get the name of the request input.
     *
     * @return string|null The name of the request input, or null if not set.
     */
    public function getName()
    {
        return data_get($this->metadata, 'name');
    }

    /**
     * Get the groups associated with the request input.
     *
     * @return array|null The groups associated with the request input.
     */
    public function getGroups()
    {
        return data_get($this->metadata, 'groups');
    }

    /**
     * Get the HTTP methods supported by the request.
     *
     * @return array|null The supported HTTP methods.
     */
    public function getMethods()
    {
        return data_get($this->metadata, 'methods');
    }

    /**
     * Get the arguments from the request input.
     *
     * @return mixed The arguments from the request input.
     */
    public function getArguments()
    {
        return data_get($this->metadata, 'arguments');
    }

    /**
     * Get the URI of the request.
     *
     * @return string|null The URI of the request, or null if not available.
     */
    public function getUri()
    {
        return data_get($this->metadata, 'uri');
    }
}
