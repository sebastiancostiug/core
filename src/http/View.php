<?php

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
    }

    /**
     * invoke()
     *
     * @param string $template Template
     * @param array  $with     Template parameters
     *
     * @return Response
     */
    public function __invoke($template = '', array $with = []) : Response
    {
        return $this->view->render($this->response, $template, $with);
        ;
    }
}
