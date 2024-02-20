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
     * Invalidates the session
     *
     * @return void
     */
    public function invalidate(): void
    {
        session_unset();
        session_destroy();
    }
}
