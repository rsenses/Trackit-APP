<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use App\Auth\AuraAuth;
use Slim\Flash\Messages;
use App\Validation\ValidatorInterface;
use Respect\Validation\Validator as v;
use Slim\Interfaces\RouterInterface;
use Exception;
use Cocur\Slugify\Slugify;
use Carbon\Carbon;
use Slim\Csrf\Guard;

use App\Entities\Product;
use App\Entities\User;

/**
 *
 */
class UserController
{
    private $auth;
    private $csrf;
    private $flash;
    private $logger;
    private $router;
    private $slugify;
    private $validator;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, AuraAuth $auth, Messages $flash, ValidatorInterface $validator, RouterInterface $router, Slugify $slugify, Guard $csrf)
    {
        $this->auth = $auth;
        $this->csrf = $csrf;
        $this->flash = $flash;
        $this->logger = $logger;
        $this->router = $router;
        $this->slugify = $slugify;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function allAction(Request $request, Response $response, array $args)
    {
        $companyId = $this->auth->getCompanyId();

        $users = User::whereHas('products', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->orderBy('last_name', 'ASC')
            ->get();

        return $this->view->render($response, 'user/all.twig', [
            'users' => $users
        ]);
    }

    public function newAction(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'user/new.twig');
    }

    public function editAction(Request $request, Response $response, array $args)
    {
        $user = User::find($args['id']);

        return $this->view->render($response, 'user/edit.twig', [
            'user' => $user
        ]);
    }

    public function saveAction(Request $request, Response $response, array $args)
    {
        $rules = [
            'first_name' => v::notEmpty(),
            'last_name' => v::notEmpty(),
            'email' => v::noWhitespace()->notEmpty()->email(),
            'phone' => v::noWhitespace()->notEmpty()->phone()
        ];

        $validation = $this->validator->validate($request, $rules);

        if ($validation->failed()) {
            $this->flash->addMessage('danger', 'El usuario no ha sido guardado, revise los errores en el formulario.');

            return $response->withRedirect($this->router->pathFor('user.new', [
                'lang' => $args['lang']
            ]));
        }

        $uuid = sprintf('%08s-%04s-%04x-%04x-%12s',
            // 32 bits for "time_low"
            substr($hash, 0, 8),

            // 16 bits for "time_mid"
            substr($hash, 8, 4),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 3
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

            // 48 bits for "node"
            substr($hash, 20, 12)
        );

        $user = User::create([
            'uuid' => $uuid,
            'first_name' => filter_var($request->getParam('first_name'), FILTER_SANITIZE_STRING),
            'last_name' => filter_var($request->getParam('last_name'), FILTER_SANITIZE_STRING),
            'email' => filter_var($request->getParam('email'), FILTER_SANITIZE_EMAIL),
            'phone' => filter_var($request->getParam('phone'), FILTER_SANITIZE_STRING),
            'is_active' => 0,
        ]);

        return $response->withRedirect($this->router->pathFor('user.edit', [
            'lang' => $args['lang'],
            'id' => $user->user_id
        ]));
    }

    public function modifyAction(Request $request, Response $response, array $args)
    {
        $rules = [
            'first_name' => v::notEmpty(),
            'last_name' => v::notEmpty(),
            'email' => v::noWhitespace()->notEmpty()->email(),
            'phone' => v::noWhitespace()->notEmpty()->phone()
        ];

        $validation = $this->validator->validate($request, $rules);

        if ($validation->failed()) {
            $this->flash->addMessage('danger', 'El usuario no ha sido guardado, revise los errores en el formulario.');

            return $response->withRedirect($this->router->pathFor('user.edit', [
                'lang' => $args['lang'],
                'id' => $args['id'],
            ]));
        }

        $user = User::find($args['id']);

        $user->first_name = filter_var($request->getParam('first_name'), FILTER_SANITIZE_STRING);
        $user->last_name = filter_var($request->getParam('last_name'), FILTER_SANITIZE_STRING);
        $user->email = filter_var($request->getParam('email'), FILTER_SANITIZE_EMAIL);
        $user->phone = filter_var($request->getParam('phone'), FILTER_SANITIZE_STRING);

        $user->save();

        return $response->withRedirect($this->router->pathFor('user.edit', [
            'lang' => $args['lang'],
            'id' => $args['id']
        ]));
    }
}
