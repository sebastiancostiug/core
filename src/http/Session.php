<?php
/**
 * @package     slim-base
 *
 * @subpackage  Session
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 *
 * @since       2024-02-20
 */

namespace core\http;

/**
 * Session class
 */
class Session
{
    /**
     * Starts the session
     *
     * @return void
     */
    public function start(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Invalidates the session
     *
     * @return void
     */
    public function invalidate(): void
    {
        session_unset();
        session_destroy();
    }

    /**
     * Sets a session variable
     *
     * @param string $key   Key
     * @param mixed  $value Value
     *
     * @return void
     */
    public function set($key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets a session variable
     *
     * @param string $key Key
     *
     * @return mixed
     */
    public function get($key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Checks if a session variable exists
     *
     * @param string $key Key
     *
     * @return boolean
     */
    public function has($key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Removes a session variable
     *
     * @param string $key Key
     *
     * @return void
     */
    public function remove($key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Flashes a session variable
     *
     * @param string $key   Key
     * @param mixed  $value Value
     *
     * @return mixed
     */
    public function flash($key = null, mixed $value = null): mixed
    {
        // Initialize flashes array if it doesn't exist
        if (!$this->has('flashes')) {
            $this->set('flashes', []);
        }

        // If a key and value are provided, set it as a flash message
        if ($key && $value) {
            $flashes = $this->get('flashes');
            $flashes[$key] = $value;
            $this->set('flashes', $flashes);
        }
        // If only a key is provided, get and remove the flash message
        else if ($key) {
            $flashes = $this->get('flashes');
            $value = $flashes[$key] ?? null;
            unset($flashes[$key]);
            $this->set('flashes', $flashes);

            return $value;
        }
    }
}
