<?php
/**
 * @package     slim-base
 *
 * @subpackage  LoadSession
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 *
 * @since       2024-02-20
 */

namespace core\bootstrap;

use core\foundation\Bootstrapper;
use core\http\Session;

/**
 * LoadSession class
 */
class LoadSession extends Bootstrapper
{
    /**
     * boot() - Boot the session bootstrapper.
     *
     * @return void Returns nothing.
     */
    public function boot(): void
    {
        $session = new Session();
        $session->start();

        $this->app->bind(Session::class, $session);
    }
}
