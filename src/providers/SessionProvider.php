<?php
/**
 * @package     slim-base
 *
 * @subpackage  SessionProvider
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 *
 * @since       2024-02-20
 */

namespace core\providers;

use core\components\ServiceProvider;
use core\http\Session;

/**
 * SessionProvider class
 */
class SessionProvider extends ServiceProvider
{
    /**
     * register()
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('session', function () {
            /*
             * Returns a closure that can be used as a session handler.
             *
             * @param mixed $key The key to retrieve or set in the session.
             * @param mixed $value The value to set in the session.
             *
             * @return core\http\Session The session handler closure or the retrieved session value.
             */

            return function ($key = null, $value = null) {
                $session = $this->app->resolve(Session::class);

                if (empty($key)) {
                    return $session;
                }

                if (empty($value)) {
                    return $session->get($key);
                }

                $session->set($key, $value);

                return $session;
            };
        });
    }

    /**
     * boot()
     *
     * @return void
     */
    public function boot()
    {
        //  placeholder
    }
}
