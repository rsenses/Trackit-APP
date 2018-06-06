<?php

namespace App\Controllers\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use App\Validation\ValidatorInterface;
use League\OAuth2\Client\Provider\GenericProvider;
use Slim\Interfaces\RouterInterface;
use Respect\Validation\Validator as v;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class AuthController
{
    private $oauth;
    private $flash;
    private $guzzle;
    private $logger;
    private $router;
    private $validator;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, Messages $flash, ValidatorInterface $validator, GenericProvider $oauth, RouterInterface $router, Client $guzzle)
    {
        $this->oauth = $oauth;
        $this->flash = $flash;
        $this->guzzle = $guzzle;
        $this->logger = $logger;
        $this->router = $router;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function getSignInAction(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'auth/signin.twig');
    }

    public function postSignInAction(Request $request, Response $response, array $args)
    {
        $validation = $this->validator->validate($request, [
            'email' => v::noWhitespace()->notEmpty()->email(),
            'password' => v::noWhitespace()->notEmpty()->length(8)->alnum('!Â·$%&/()=?Â¿Â¡^*+[]Â¨{},;.:-_#@'),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        try {
            // Try to get an access token using the resource owner password credentials grant.
            $accessToken = $this->oauth->getAccessToken('password', [
                'username' => filter_var($request->getParam('email'), FILTER_SANITIZE_EMAIL),
                'password' => filter_var($request->getParam('password'), FILTER_SANITIZE_STRING)
            ]);

            $_SESSION['accessToken'] = $accessToken;
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            $this->flash->addMessage('danger', 'Email o contraseña no válidos.');

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        try {
            $apiRequest = $this->guzzle->request('GET', 'products/date', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['accessToken']->getToken(),
                    'Content-Language' => 'es'
                ]
            ]);

            $product = json_decode($apiRequest->getBody());
        } catch (BadResponseException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                $this->flash->addMessage('danger', 'Ningún producto asignado.');
            } else {
                throw new \Exception($e->getMessage(), 500);
            }

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        return $response->withRedirect($this->router->pathFor('product.registrations', ['id' => $product->id]));
    }

    public function getSignOutAction(Request $request, Response $response, array $args)
    {
        session_unset();
        session_destroy();

        return $response->withRedirect($this->router->pathFor('auth.signin'));
    }
}
