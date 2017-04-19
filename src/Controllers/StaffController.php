<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use App\Auth\AuraAuth;
use App\Validation\ValidatorInterface;
use Respect\Validation\Validator as v;
use Exception;
use Slim\Interfaces\RouterInterface;
use Slim\Csrf\Guard;
use Carbon\Carbon;

use App\Entities\Customer;
use App\Entities\Product;
use App\Entities\User;

/**
 *
 */
class StaffController
{
    private $auth;
    private $csrf;
    private $flash;
    private $logger;
    private $router;
    private $validator;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, AuraAuth $auth, Messages $flash, ValidatorInterface $validator, RouterInterface $router, Guard $csrf)
    {
        $this->auth = $auth;
        $this->csrf = $csrf;
        $this->flash = $flash;
        $this->logger = $logger;
        $this->router = $router;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function addAction(Request $request, Response $response, array $args)
    {
        try {
            $product = Product::where('company_id', $this->auth->getCompanyId())
                ->where('product_id', $args['id'])
                ->firstOrFail();
        } catch (\Exception $e) {
            $this->flash->addMessage('danger', $e->getMessage());

            return $response->withRedirect($this->router->pathFor('dashboard', [
                'lang' => $args['lang']
            ]));
        }

        $users = User::whereHas('products', function ($query) {
                $query->where('company_id', $this->auth->getCompanyId());
            })
            ->get();

        $staff = $request->getParam('staff');

        $staffValidator = v::arrayType()->each(v::intVal())->validate($staff);

        if ($staffValidator) {
            foreach ($staff as $userId) {
                $user = User::find($userId);

                $product->users()->attach($user, [
                    'date_start' => $product->date_start,
                    'date_end' => $product->date_end
                ]);
            }

            return $response->withJson([
                'status' => 'success'
            ]);
        } else {
            return $response->withJson([
                'status' => 'error',
                'errors' => 'Error al asignar usuarios, inténtelo de nuevo más tarde.',
                'csrf_name' => $this->csrf->getTokenName(),
                'csrf_value' => $this->csrf->getTokenValue()
            ]);
        }
    }

    public function removeAction(Request $request, Response $response, array $args)
    {
        $product = Product::find($args['product']);

        $product->users()->detach($args['staff']);
    }

    public function modifyDateStartAction(Request $request, Response $response, array $args)
    {
        $product = Product::findOrFail($args['product']);

        $product->users()
            ->updateExistingPivot($args['staff'], [
                'date_start' => Carbon::createFromFormat('Y-m-d H:i:s', $request->getParam('date_start'))
            ]);

        return $response->withHeader('X-IC-Redirect', $this->router->pathFor('registration.all', [
            'lang' => $args['lang'],
            'id' => $product->product_id,
        ]));
    }

    public function modifyDateEndAction(Request $request, Response $response, array $args)
    {
        $product = Product::findOrFail($args['product']);

        $product->users()
            ->updateExistingPivot($args['staff'], [
                'date_end' => Carbon::createFromFormat('Y-m-d H:i:s', $request->getParam('date_end'))
            ]);

        return $response->withHeader('X-IC-Redirect', $this->router->pathFor('registration.all', [
            'lang' => $args['lang'],
            'id' => $product->product_id,
        ]));
    }
}
