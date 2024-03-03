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

use InvalidArgumentException;

/**
 * Represents a behavior component.
 *
 * This class extends the Component class and provides additional functionality for component's behavior.
 */
class Behavior extends Component
{
    /**
     * @var mixed The owner of this behavior.
     */
    public $owner;

    /**
     * @var array The events attached to this behavior.
     */
    private $_attachedEvents = [];

    /**
     * Returns the events associated with this behavior.
     *
     * @return array The events associated with this behavior.
     */
    public function events()
    {
        return [];
    }

    /**
     * Attaches the behavior to the specified owner.
     *
     * @param mixed $owner The owner to attach the behavior to.
     *
     * @return void
     */
    public function attach(mixed $owner)
    {
        $this->owner = $owner;
        foreach ($this->events() as $event => $handler) {
            $this->_attachedEvents[$event] = $handler;
            if (is_string($handler) && method_exists($this, $handler)) {
                $owner->on($event, [$this, $handler]);
            } elseif (is_callable($handler)) {
                $owner->on($event, $handler);
            } else {
                throw new InvalidArgumentException('Handler must be a valid callable');
            }
        }
    }

    /**
     * Detaches the behavior from the component.
     *
     * @return void
     */
    public function detach()
    {
        if ($this->owner) {
            foreach ($this->_attachedEvents as $event => $handler) {
                $this->owner->off($event, is_string($handler) ? [$this, $handler] : $handler);
            }
            $this->_attachedEvents = [];
            $this->owner = null;
        }
    }
}
