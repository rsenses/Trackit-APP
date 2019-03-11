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
use Exception;
use GuzzleHttp\Exception\ClientException;

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

    public function indexAction(Request $request, Response $response)
    {
        return $this->view->render($response, 'product/select.twig', [
            'products' => $_SESSION['products']
        ]);
    }

    public function selectAction(Request $request, Response $response, array $args)
    {
        $product = $_SESSION['product'] = $_SESSION['products'][$args['id']];

        $action = $request->getQueryParam('action');

        $path = 'product.' . $action;

        return $response->withRedirect($this->router->pathFor($path, ['id' => $product->product_id]));
    }

    public function infoAction(Request $request, Response $response, array $args)
    {
        try {
            $apiRequest = $this->guzzle->request('GET', 'products/' . $args['id'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['accessToken'],
                    'Content-Language' => 'es',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ]);

            return $response->withJson(json_decode($apiRequest->getBody()));
        } catch (ClientException $e) {
            return $response->withJson(json_decode($e->getResponse()->getBody(), true));
        } catch (Exception $e) {
            $this->flash->addMessage('danger', $e->getMessage());

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

    public function searchAction(Request $request, Response $response, array $args)
    {
        $product = $_SESSION['product'];

        $registrations = null;
        $apiResponse = null;

        try {
            $apiRequest = $this->guzzle->request('GET', 'registrations', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['accessToken'],
                    'Content-Language' => 'es',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'query' => [
                    'product_id' => $product->product_id
                ]
            ]);

            $registrations = json_decode($apiRequest->getBody());
        } catch (ClientException $e) {
            $apiResponse = json_decode($e->getResponse()->getBody());
            error_log($e->getResponse()->getBody());
            error_log($e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->flash->addMessage('danger', $e->getMessage());

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        return $this->view->render($response, 'product/search.twig', [
            'response' => $apiResponse,
            'registrations' => $registrations,
            'product' => $product,
        ]);
    }

    public function laserScanAction(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'product/laserscan.twig', [
            'product_id' => $args['id'],
        ]);
    }
}
