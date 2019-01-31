<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use GuzzleHttp\Client;
use Slim\Collection;
use GuzzleHttp\Exception\ClientException;
use Exception;
use Slim\Interfaces\RouterInterface;
use Slim\Csrf\Guard;
use App\Validation\ValidatorInterface;
use Respect\Validation\Validator as v;

/**
 *
 */
class RegistrationController
{
    private $csrf;
    private $flash;
    private $guzzle;
    private $logger;
    private $router;
    private $settings;
    private $validator;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, Messages $flash, Client $guzzle, RouterInterface $router, Guard $csrf, ValidatorInterface $validator, Collection $settings)
    {
        $this->csrf = $csrf;
        $this->flash = $flash;
        $this->guzzle = $guzzle;
        $this->logger = $logger;
        $this->router = $router;
        $this->settings = $settings;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function createAction(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'registration/guests.twig', [
            'product_id' => $args['id']
        ]);
    }

    public function inscriptionsAction(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'registration/assistants.twig', [
            'product_id' => $args['id']
        ]);
    }

    public function saveAction(Request $request, Response $response, array $args)
    {
        $validation = $this->validator->validate($request, [
            'first_name' => v::notEmpty(),
            'last_name' => v::notEmpty(),
            'registration_type' => v::notEmpty(),
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
            $apiRequest = $this->guzzle->request('POST', 'registrations', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['accessToken'],
                    'Content-Language' => 'es',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'first_name' => $request->getParam('first_name'),
                    'last_name' => $request->getParam('last_name'),
                    'email' => $request->getParam('email'),
                    'nif' => $request->getParam('nif'),
                    'company' => $request->getParam('company'),
                    'position' => $request->getParam('position'),
                    'product_id' => $args['id'],
                    'registration_type' => $request->getParam('registration_type_id'),
                    'transition' => $request->getParam('verification') ? 'verify' : 'approve',
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
            $apiRequest = $this->guzzle->request('POST', 'verifications', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['accessToken'],
                    'Content-Language' => 'es',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'unique_id' => $args['qr']
                ]
            ]);

            return $response->withJson(json_decode($apiRequest->getBody(), true));
        } catch (ClientException $e) {
            $guzzleResponse = $e->getResponse();
            $body = json_decode($guzzleResponse->getBody());

            return $response->withJson([
                'error-level' => $guzzleResponse->getHeader('Error-Level')[0],
                'message' => $body->message
            ]);
        } catch (Exception $e) {
            $this->flash->addMessage('danger', $e->getMessage());

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }
}
