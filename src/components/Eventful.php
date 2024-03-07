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
 * Represents an Eventful component.
 *
 * This class extends the Component class and implements the SplSubject interface.
 * It can be used to create event-driven functionality.
 */
class Eventful extends \common\Component implements \SplSubject
{
    /**
     * The private property that holds the observers for the Eventful component.
     *
     * @var array
     */
    private $_observers = [];

    /**
     * The private property that holds the event for the Eventful component.
     *
     * @var mixed
     */
    private $_event;

    /**
     * Attaches an observer to the subject.
     *
     * @param \SplObserver $observer The observer to attach.
     *
     * @return void
     */
    public function attach(\SplObserver $observer): void
    {
        $this->_observers[] = $observer;
    }

    /**
     * Detaches an observer from the subject.
     *
     * @param \SplObserver $observer The observer to detach.
     *
     * @return void
     */
    public function detach(\SplObserver $observer): void
    {
        $this->_observers = array_filter($this->_observers, function ($a) use ($observer) {
            return $a !== $observer;
        });
    }

    /**
     * Notifies the event.
     *
     * This method is responsible for triggering the event and notifying any registered listeners.
     * It does not return any value.
     *
     * @param mixed $event The event to notify.
     *
     * @return void
     */
    public function notify(mixed $event = null): void
    {
        $this->_event = $event;

        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Returns the event.
     *
     * This method is responsible for returning the event.
     *
     * @return mixed
     */
    public function getEvent(): mixed
    {
        return $this->_event;
    }
}
