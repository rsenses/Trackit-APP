<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use League\OAuth2\Client\Provider\GenericProvider;
use App\Upload\Upload;
use Slim\Flash\Messages;
use Slim\Collection;
use App\Validation\ValidatorInterface;
use Slim\Interfaces\RouterInterface;
use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use App\Entities\Product;
use App\Entities\User;
use App\Entities\Place;

/**
 *
 */
class ProductController
{
    private $oauth;
    private $flash;
    private $logger;
    private $router;
    private $guzzle;
    private $settings;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, GenericProvider $oauth, Messages $flash, RouterInterface $router, Client $guzzle, Collection $settings)
    {
        $this->oauth = $oauth;
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
            $apiRequest = $this->guzzle->request('GET', 'products/info/'.$args['id'], [
                'headers' => [
                    'Authorization' => 'Bearer '.$_SESSION['accessToken']->getToken(),
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

    public function allRegistrationsAction(Request $request, Response $response, array $args)
    {
        try {
            $apiRequest = $this->guzzle->request('GET', 'products/'.$args['id'].'/registrations/all', [
                'headers' => [
                    'Authorization' => 'Bearer '.$_SESSION['accessToken']->getToken(),
                    'Content-Language' => 'es'
                ]
            ]);

            $registrations = json_decode($apiRequest->getBody());

            return $this->view->render($response, 'product/registrations.twig', [
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
}
