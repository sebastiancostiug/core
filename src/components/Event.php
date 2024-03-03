<?php
/**
 * @package     slim-base
 *
 * @subpackage  Event
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
 * Event class
 */
class Event extends \common\Component
{
    /**
     * @var array The event handlers for the component
     */
    protected static $events = [];

    /**
     * Attach an event handler to a class.
     *
     * @param string   $class   The class name.
     * @param string   $name    The event name.
     * @param callable $handler The event handler function.
     * @param mixed    $data    Optional data to pass to the event handler.
     * @param boolean  $append  Whether to append the event handler to the existing ones or replace them.
     *
     * @return void
     */
    public static function on($class, $name, callable $handler, mixed $data = null, $append = true)
    {
        if ($append || empty(self::$events[$name][$class])) {
            self::$events[$name][$class][] = [$handler, $data];
        } else {
            array_unshift(self::$events[$name][$class], [$handler, $data]);
        }
    }

    /**
     * Removes an event handler for the specified event name.
     *
     * @param string        $class   The class name.
     * @param string        $name    The event name.
     * @param callable|null $handler The event handler to be removed. If not provided, all event handlers for the specified event name will be removed.
     *
     * @return void
     */
    public static function off($class, $name, $handler = null)
    {
        if (empty(self::$events[$name][$class])) {
            return;
        }

        if ($handler === null) {
            unset(self::$events[$name][$class]);
        } else {
            foreach (self::$events[$name][$class] as $i => $event) {
                if ($event[0] === $handler) {
                    unset(self::$events[$name][$class][$i]);
                }
            }
        }
    }

    /**
     * Triggers an event.
     *
     * @param string $class The class name.
     * @param string $name  The event name.
     * @param mixed  $event An Event object or an event name.
     *
     * @return void
     */
    public static function trigger($class, $name, mixed $event = null)
    {
        if (empty(self::$events[$name][$class])) {
            return;
        }

        if ($event === null) {
            $event = new static();
        } elseif (is_string($event)) {
            $event = new static(['name' => $event]);
        }

        $event->name = $name;

        foreach (self::$events[$name][$class] as $handler) {
            $event->data = $handler[1];
            call_user_func($handler[0], $event);
            if ($event->handled) {
                return;
            }
        }
    }
}
