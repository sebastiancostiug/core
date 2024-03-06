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
     * @var string $name The name of the event.
     */
    protected string $name;

    /**
     * @var mixed $sender The object that raises the event.
     */
    protected mixed $sender;

    /**
     * @var callable $handler The event handler.
     */
    protected callable $handler;

    /**
     * @var mixed $data Optional. Additional data to pass to the event handler.
     */
    protected mixed $data;

    /**
     * @var boolean $append Optional. Whether to append the event handler to the existing handlers or replace them.
     */
    protected bool $append;

    /**
     * Event constructor
     *
     * @param string   $name    The name of the event.
     * @param callable $handler The event handler function.
     * @param mixed    $data    Optional. Additional data to pass to the event handler.
     * @param boolean  $append  Optional. Whether to append the event handler to the existing handlers or replace them.
     *
     * @return void
     */
    public function __construct(string $name, callable $handler, mixed $data = null, bool $append = true)
    {
        $this->name    = $name;
        $this->handler = $handler;
        $this->data    = $data;
        $this->append  = $append;
    }

        /**
     * Get the name of the event.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of the event.
     *
     * @param string $name The name to set.
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the sender of the event.
     *
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set the sender of the event.
     *
     * @param mixed $sender The sender to set.
     *
     * @return void
     */
    public function setSender(mixed $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * Get the handler of the event.
     *
     * @return callable
     */
    public function getHandler(): callable
    {
        return $this->handler;
    }

    /**
     * Set the handler of the event.
     *
     * @param callable $handler The handler to set.
     *
     * @return void
     */
    public function setHandler(callable $handler): void
    {
        $this->handler = $handler;
    }

    /**
     * Get the data of the event.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data of the event.
     *
     * @param mixed $data The data to set.
     *
     * @return void
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     * Check if the handler should be appended.
     *
     * @return boolean
     */
    public function isAppend(): bool
    {
        return $this->append;
    }

    /**
     * Set whether the handler should be appended.
     *
     * @param boolean $append
     *
     * @return void
     */
    public function setAppend(bool $append): void
    {
        $this->append = $append;
    }

    /**
     * Trigger the event.
     *
     * @return void
     * @throws \Exception if the event handler function call fails.
     */
    public function trigger()
    {
        if (false === call_user_func($this->handler, $this)) {
            throw new \Exception('Event handler function call failed.');
        }
    }
}
