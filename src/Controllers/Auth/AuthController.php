<?php

namespace App\Controllers\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use App\Validation\ValidatorInterface;
use Slim\Interfaces\RouterInterface;
use Respect\Validation\Validator as v;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AuthController
{
    private $flash;
    private $guzzle;
    private $logger;
    private $router;
    private $validator;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, Messages $flash, ValidatorInterface $validator, RouterInterface $router, Client $guzzle)
    {
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
            $loginRequest = $this->guzzle->request('POST', 'login', [
                'headers' => [
                    'Content-Language' => 'es',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'email' => $request->getParam('email'),
                    'password' => $request->getParam('password'),
                ]
            ]);

            $loginResponse = json_decode($loginRequest->getBody());

            $_SESSION['accessToken'] = $loginResponse->api_token;
        } catch (\Throwable $e) {
            $this->flash->addMessage('danger', 'Email o contraseña no válidos.');

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        try {
            $productsRequest = $this->guzzle->request('GET', 'products', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['accessToken'],
                    'Content-Language' => 'es',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ]);

            $_SESSION['products'] = $products = json_decode($productsRequest->getBody());
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400 || $e->getResponse()->getStatusCode() === 404) {
                $message = 'Ningún producto asignado.';
            } else {
                $message = $e->getMessage();
            }

            $this->flash->addMessage('danger', $message);

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        } catch (\Throwable $th) {
            error_log($th->getMessage());
        }

        if (count($products) > 1) {
            return $response->withRedirect($this->router->pathFor('product.index'));
        } else {
            $device = $request->getQueryParam('device');

            if ($device == 'mobile') {
                $path = 'product.laserscan';
            } else {
                $path = 'product.search';
            }

            $_SESSION['product'] = $products[0];

            return $response->withRedirect($this->router->pathFor($path, ['id' => $products[0]->product_id]));
        }
    }

    public function getSignOutAction(Request $request, Response $response, array $args)
    {
        session_unset();
        session_destroy();

        return $response->withRedirect($this->router->pathFor('auth.signin'));
    }
}
