<?php
/**
 * @package     slim-base
 *
 * @subpackage  Component
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 *
 * @since       2024-03-03
 */

namespace core\components;

/**
 * Component class
 */
class Component extends \common\Component
{
    /**
     * @var array The event handlers for the component
     */
    protected $events = [];

    public function __construct()
    {
        $this->attachBehaviors($this->behaviors());
    }

    /**
     * Attach an event handler to the component.
     *
     * @param string   $name    The name of the event.
     * @param callable $handler The event handler function.
     * @param mixed    $data    Optional. Additional data to pass to the event handler.
     * @param boolean  $append  Optional. Whether to append the event handler to the existing handlers or replace them.
     *
     * @return void
     */
    public function on($name, callable $handler, mixed $data = null, $append = true)
    {
        if ($append || empty($this->events[$name])) {
            $this->events[$name][] = [$handler, $data];
        } else {
            array_unshift($this->events[$name], [$handler, $data]);
        }
    }

    /**
     * Removes an event handler for the specified event name.
     *
     * @param string        $name    The name of the event.
     * @param callable|null $handler The event handler to be removed. If not provided, all event handlers for the specified event name will be removed.
     * @return void
     */
    public function off($name, $handler = null)
    {
        if (empty($this->events[$name])) {
            return;
        }

        if ($handler === null) {
            unset($this->events[$name]);
            return;
        }

        foreach ($this->events[$name] as $i => $event) {
            if ($event[0] === $handler) {
                unset($this->events[$name][$i]);
            }
        }
    }

    /**
     * Triggers an event.
     *
     * This method represents the happening of an event. It invokes all registered handlers for the event.
     *
     * @param string $name  The event name.
     * @param Event  $event The event parameter. If not set, a default Event object will be created.
     *
     * @return void
     */
    public function trigger($name, Event $event = null)
    {
        if (empty($this->events[$name])) {
            return;
        }

        if ($event === null) {
            $event = new Event();
        }

        if ($event->sender === null) {
            $event->sender = $this;
        }

        $event->handled = false;
        $event->name = $name;

        foreach ($this->events[$name] as $handler) {
            $event->data = $handler[1];
            call_user_func($handler[0], $event);
            if ($event->handled) {
                return;
            }
        }
    }

    /**
     * Returns the behaviors that should be attached to this component.
     *
     * @return array the behaviors that should be attached to this component.
     */
    public function behaviors()
    {
        return [];
    }

    /**
     * Retrieves the behavior with the specified name.
     *
     * @param string $name The name of the behavior to retrieve.
     * @return Behavior|null The behavior instance, or null if not found.
     */
    public function getBehavior($name)
    {
        return $this->behaviors[$name] ?? null;
    }

    /**
     * Retrieves the behaviors associated with this component.
     *
     * @return array The behaviors associated with this component.
     */
    public function getBehaviors()
    {
        return $this->behaviors();
    }

    /**
     * Checks if the component has a behavior with the given name.
     *
     * @param string $name The name of the behavior to check.
     * @return boolean Returns true if the component has the behavior, false otherwise.
     */
    public function hasBehavior($name)
    {
        return isset($this->behaviors[$name]);
    }

    /**
     * Attaches a behavior to the component.
     *
     * @param string $name     The name of the behavior.
     * @param object $behavior The behavior object to attach.
     * @return void
     */
    public function attachBehavior($name, object $behavior)
    {
        if ($behavior instanceof Behavior) {
            $behavior->attach($this);
        } else {
            $this->attachBehavior($name, new $behavior());
        }
    }

    /**
     * Attaches behaviors to the component.
     *
     * @param array $behaviors The behaviors to be attached.
     * @return void
     */
    public function attachBehaviors(array $behaviors)
    {
        foreach ($behaviors as $name => $behavior) {
            $this->attachBehavior($name, $behavior);
        }
    }

    /**
     * Detaches a behavior from the component.
     *
     * @param string $name The name of the behavior to detach.
     * @return void
     */
    public function detachBehavior($name)
    {
        if (isset($this->behaviors[$name])) {
            $this->behaviors[$name]->detach();
            unset($this->behaviors[$name]);
        }
    }

    /**
     * Detaches all behaviors from the component.
     *
     * @return void
     */
    public function detachBehaviors()
    {
        foreach ($this->behaviors as $name => $behavior) {
            $this->detachBehavior($name);
        }
    }
}
