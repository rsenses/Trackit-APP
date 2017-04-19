<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;
use League\OAuth2\Client\Provider\GenericProvider;
use Slim\Interfaces\RouterInterface;

class AuthenticatedMiddleware
{
    private $oauth;
    private $flash;
    private $router;

    public function __construct(GenericProvider $oauth, Messages $flash, RouterInterface $router)
    {
        $this->oauth = $oauth;
        $this->flash = $flash;
        $this->router = $router;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $existingAccessToken = $_SESSION['accessToken'] ?? null;

        if (!$existingAccessToken || !$existingAccessToken->getRefreshToken()) {
            $this->flash->addMessage('warning', 'Please sign in before using the app.');

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        } else {
            if ($existingAccessToken->hasExpired()) {
                $newAccessToken = $this->oauth->getAccessToken('refresh_token', [
                    'refresh_token' => $existingAccessToken->getRefreshToken()
                ]);

                $_SESSION['accessToken'] = $newAccessToken;
                $existingAccessToken = $newAccessToken;
            }
        }

        return $next($request, $response);
    }
}
