<?php
/**
 * @package     Core
 *
 * @subpackage  View
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    View
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023-10-17
 */

namespace core\http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Slim\Views\PhpRenderer;

/**
 * View class
 */
class View
{
    /**
     * @var Response $response Response
     */
    protected Response $response;

    /**
     * @var PhpRenderer $view Latte engine
     */
    protected PhpRenderer $view;

    /**
     * @var string $this->theme Theme name
     */
    protected string $theme = 'default';

    /**
     * __construct()
     *
     * @param Psr17Factory $factory REsponse factory
     *
     * @return void
     */
    public function __construct(Psr17Factory $factory)
    {
        $this->response = $factory->createResponse(200, 'Success');

        $this->view = new PhpRenderer();

        $this->theme = config('theme.selected');
    }

    /**
     * invoke()
     *
     * @param string $template Template name to render
     * @param array  $with     Template parameters to pass
     * @param string $layout   Layout template name to render
     *
     * @return Response
     */
    public function __invoke($template = '', array $with = [], $layout = 'main') : Response
    {
        $assets = app()->assets($layout);

        if ($layout) {
            $layoutPath = config("theme.{$this->theme}.views_path") . DIRECTORY_SEPARATOR . '_layouts' . DIRECTORY_SEPARATOR;
            $this->view->setLayout($layoutPath . $layout . '.php');
        }

        $with['css'] = $assets->output('css');
        $with['js'] = $assets->output('js');

        $viewPath = config("theme.{$this->theme}.views_path") . DIRECTORY_SEPARATOR;

        return $this->view->render($this->response, $viewPath . $template . '.php', $with);
    }

    /**
     * randomPage()
     *
     * @param string $path Path to views folder
     *
     * @return string
     */
    public function randomPage($path = '')
    {
        $folder = scandir(views_path("{$this->theme}/{$path}"));
        $views = array_slice($folder, 2, count($folder));
        $views = array_map(function ($view) use ($path) {
            return $path . DIRECTORY_SEPARATOR . $view;
        }, $views);

        return str_replace('.php', '', $views[array_rand($views)]);
    }
}
