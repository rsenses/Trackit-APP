<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Slim\Collection;
use Slim\Interfaces\RouterInterface;
use GuzzleHttp\Client;

/**
 *
 */
class ProductController
{
    private $flash;
    private $logger;
    private $router;
    private $guzzle;
    private $settings;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, Messages $flash, RouterInterface $router, Client $guzzle, Collection $settings)
    {
        $this->flash = $flash;
        $this->logger = $logger;
        $this->router = $router;
        $this->settings = $settings;
        $this->guzzle = $guzzle;
        $this->view = $view;
    }

    public function infoAction(Request $request, Response $response, array $args)
    {
        try {
            $apiRequest = $this->guzzle->request('GET', 'products/info/' . $args['id'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['accessToken']->getToken(),
                    'Content-Language' => 'es'
                ]
            ]);

            return $response->withJson(json_decode($apiRequest->getBody(), true));
        } catch (ClientException $e) {
            return $response->withJson(json_decode($e->getResponse()->getBody(), true));
        } catch (BadResponseException $e) {
            $this->flash->addMessage('danger', $e->getMessage());

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

    public function searchAction(Request $request, Response $response, array $args)
    {
        try {
            $apiRequest = $this->guzzle->request('GET', 'products/' . $args['id'] . '/registrations/all', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['accessToken']->getToken(),
                    'Content-Language' => 'es'
                ]
            ]);

            $registrations = json_decode($apiRequest->getBody(), true);

            return $this->view->render($response, 'product/search.twig', [
                'registrations' => $registrations,
                'product_id' => $args['id'],
            ]);
        } catch (ClientException $e) {
            return $response->withJson(json_decode($e->getResponse()->getBody(), true));
        } catch (BadResponseException $e) {
            $this->flash->addMessage('danger', $e->getMessage());

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

    public function laserScanAction(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'product/laserscan.twig', [
            'product_id' => $args['id'],
        ]);
    }

    public function cameraScanAction(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'product/camerascan.twig', [
            'product_id' => $args['id'],
        ]);
    }
}
