<?php
/**
 * @package     Core
 *
 * @subpackage  Redirect class
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    http
 *
 * @since       2024-01-27
 */

namespace core\http\routing;

use core\http\RequestInput;

/**
 * Routing class
 */
class Routing
{
    /**
     * redirect
     *
     * @param string $location Location
     *
     * @return mixed
     * @throws \Throwable if an error occurs during the redirection process.
     */
    public function redirect($location)
    {
        try {
            $redirect = app()->resolve(Redirect::class);

            return $redirect($location);
        } catch (\Throwable $th) {
            throw_when(true, [$th->getMessage()]);
        }
    }

    /**
     * Redirects the user back to the previous page.
     *
     * @return mixed
     * @throws \Throwable if an error occurs during the redirection process.
     */
    public function back()
    {
        try {
            $routeInput = app()->resolve(RequestInput::class);
            $path = $routeInput->getUri()->getPath();

            return $this->redirect($path);
        } catch (\Throwable $th) {
            throw_when(true, [$th->getMessage()]);
        }
    }

    /**
     * Returns the referer of the request.
     *
     * @return string
     */
    public function referer(): string
    {
        $routeInput = app()->resolve(RequestInput::class);

        return $routeInput->getReferer() ?? '/';
    }

    /**
     * Redirects the user to the root page.
     *
     * @return mixed
     * @throws \Throwable if an error occurs during the redirection process.
     */
    public function root()
    {
        try {
            return $this->redirect('/');
        } catch (\Throwable $th) {
            throw_when(true, [$th->getMessage()]);
        }
    }

    /**
     * Redirects the user to the login page.
     *
     * @return mixed
     * @throws \Throwable if an error occurs during the redirection process.
     */
    public function login()
    {
        try {
            return $this->redirect('/account/login');
        } catch (\Throwable $th) {
            throw_when(true, [$th->getMessage()]);
        }
    }
}
