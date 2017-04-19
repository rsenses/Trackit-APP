<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;

/**
 *
 */
class HomeController
{
    private $flash;
    private $logger;
    private $router;
    private $view;

    public function __construct(Messages $flash, LoggerInterface $logger, RouterInterface $router, Twig $view)
    {
        $this->flash = $flash;
        $this->logger = $logger;
        $this->router = $router;
        $this->view = $view;
    }

    public function indexAction(Request $request, Response $response, array $args)
    {
        // Sample log message
        // $this->logger->info("Slim '/' route with DI");

        // Render index view
        return $response->withRedirect($this->router->pathFor('auth.signin'));
    }
}
