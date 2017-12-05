<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use League\OAuth2\Client\Provider\GenericProvider;
use GuzzleHttp\Client;
use Slim\Collection;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerErrorResponseException;
use Exception;
use Slim\Interfaces\RouterInterface;
use Slim\Csrf\Guard;
use Carbon\Carbon;
use App\Validation\ValidatorInterface;
use Respect\Validation\Validator as v;

use App\Entities\Customer;
use App\Entities\Product;
use App\Entities\Registration;
use App\Entities\User;
use App\Entities\Verification;

/**
 *
 */
class RegistrationController
{
    private $csrf;
    private $flash;
    private $guzzle;
    private $logger;
    private $oauth;
    private $router;
    private $settings;
    private $validator;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, GenericProvider $oauth, Messages $flash, Client $guzzle, RouterInterface $router, Guard $csrf, ValidatorInterface $validator, Collection $settings)
    {
        $this->csrf = $csrf;
        $this->flash = $flash;
        $this->guzzle = $guzzle;
        $this->logger = $logger;
        $this->oauth = $oauth;
        $this->router = $router;
        $this->settings = $settings;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function productOldAction(Request $request, Response $response, array $args)
    {
        try {
            $apiRequest = $this->guzzle->request('GET', 'products/info/'.$args['id'], [
                'headers' => [
                    'Authorization' => 'Bearer '.$_SESSION['accessToken']->getToken(),
                    'Content-Language' => 'es'
                ]
            ]);

            return $this->view->render($response, 'registration/scan.twig', [
                'product' => json_decode($apiRequest->getBody())
            ]);
        } catch (ClientException $e) {
            return $response->withJson(json_decode($e->getResponse()->getBody(), true));
        } catch (BadResponseException $e) {
            $this->flash->addMessage('danger', $e->getMessage());

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

    public function productAction(Request $request, Response $response, array $args)
    {
        try {
            $apiRequest = $this->guzzle->request('GET', 'products/info/'.$args['id'], [
                'headers' => [
                    'Authorization' => 'Bearer '.$_SESSION['accessToken']->getToken(),
                    'Content-Language' => 'es'
                ]
            ]);

            return $this->view->render($response, 'registration/scan.twig', [
                'product' => json_decode($apiRequest->getBody()),
                'product_id' => $args['id']
            ]);
        } catch (ClientException $e) {
            // if ($e->getResponse()->getBody() === 401) {
            //     $this->flash->addMessage('danger', json_decode($e->getResponse()->getBody(), true)->message);

            //     return $response->withRedirect($this->router->pathFor('auth.signin'));
            // }

            return $response->withJson(json_decode($e->getResponse()->getBody(), true));
        } catch (BadResponseException $e) {
            $this->flash->addMessage('danger', $e->getMessage());

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

    public function createAction(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'registration/create.twig', [
            'product_id' => $args['id'],
            'registration_type' => $this->settings['enum']['registration_type']
        ]);
    }

    public function saveAction(Request $request, Response $response, array $args)
    {
        $validation = $this->validator->validate($request, [
            'first_name' => v::notEmpty(),
            'last_name' => v::notEmpty(),
            'registration_type_id' => v::notEmpty(),
            'email' => v::noWhitespace()->notEmpty()->email()
        ]);

        if ($validation->failed()) {
            $errors = $_SESSION['validationErrors'];

            unset($_SESSION['validationErrors']);

            return $response->withJson([
                'status' => 'error',
                'errors' => $errors,
                'csrf_name' => $this->csrf->getTokenName(),
                'csrf_value' => $this->csrf->getTokenValue()
            ]);
        }

        try {
            $apiRequest = $this->guzzle->request('POST', 'inscriptions/create/', [
                'headers' => [
                    'Authorization' => 'Bearer '.$_SESSION['accessToken']->getToken(),
                    'Content-Language' => 'es'
                ],
                'form_params' => [
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'],
                    'nif' => $_POST['nif'] ?? null,
                    'company' => $_POST['company'] ?? null,
                    'position' => $_POST['position'] ?? null,
                    'product_id' => $args['id'],
                    'registration_type_id' => $_POST['registration_type_id'],
                    'verification' => $_POST['verification']
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

    public function verifyAction(Request $request, Response $response, array $args)
    {

        try {
            $apiRequest = $this->guzzle->request('GET', 'inscriptions/verify/'.$args['qr'], [
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

    public function toggleVerificationAction(Request $request, Response $response, array $args)
    {
        try {
            $apiRequest = $this->guzzle->request('GET', 'inscriptions/verify/'.$args['qr'], [
                'headers' => [
                    'Authorization' => 'Bearer '.$_SESSION['accessToken']->getToken(),
                    'Content-Language' => 'es'
                ]
            ]);

            return (string) $apiRequest->getBody();
        } catch (ClientException $e) {
            return $response->withJson(json_decode($e->getResponse()->getBody(), true));
        } catch (BadResponseException $e) {
            $this->flash->addMessage('danger', $e->getMessage());

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }
}
