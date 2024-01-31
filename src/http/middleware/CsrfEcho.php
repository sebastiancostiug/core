<?php
/**
 *
 * @package     slim-base
 *
 * @subpackage  CsrfEcho
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    slim-base
 * @see
 *
 * @since       2024-01-31
 *
 */

namespace core\http\middleware;

/**
 * CsrfEcho class
 */
class CsrfEcho
{
    /**
     * @var string $_csrfNameKey The name key used for CSRF protection.
     */
    private $_csrfNameKey;

    /**
     * @var string $_csrfValueKey The value key used for CSRF protection.
     */
    private $_csrfValueKey;

    /**
     * @var string $_csrfName The CSRF token name.
     */
    private $_csrfName;

    /**
     * @var string $_csrfValue The CSRF token value.
     */
    private $_csrfValue;

    /**
     * CsrfEcho constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $csrf = app()->resolve('csrf');

        $this->_csrfNameKey  = $csrf->getTokenNameKey();
        $this->_csrfValueKey = $csrf->getTokenValueKey();
        $this->_csrfName     = $csrf->getTokenName();
        $this->_csrfValue    = $csrf->getTokenValue();
    }

    /**
     * Invokes the CSRF Echo middleware.
     *
     * This middleware generates hidden input fields containing the CSRF token name and value.
     * These fields can be included in HTML forms to protect against cross-site request forgery attacks.
     *
     * @return string The HTML code for the CSRF token input fields.
     */
    public function __invoke()
    {
        return <<<HTML
            <input type="hidden" name="{$this->_csrfNameKey}" value="{$this->_csrfName}">
            <input type="hidden" name="{$this->_csrfValueKey}" value="{$this->_csrfValue}">
        HTML;
    }

    /**
     * Returns the HTML code for the CSRF token input fields.
     *
     * @return string The HTML code for the CSRF token input fields.
     */
    public static function html()
    {
        return (new static())();
    }

    /**
     * Returns the HTML code for the CSRF token input fields.
     *
     * @param string $name      The name of the method.
     * @param array  $arguments The arguments passed to the method.
     *
     * @return string The HTML code for the CSRF token input fields.
     */
    public static function __callStatic($name, array $arguments)
    {
        return (new static())();
    }
}
