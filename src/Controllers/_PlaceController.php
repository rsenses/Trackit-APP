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

use App\Entities\Place;
use App\Entities\State;

/**
 *
 */
class PlaceController
{
    private $auth;
    private $flash;
    private $logger;
    private $router;
    private $slugify;
    private $validator;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, AuraAuth $auth, Messages $flash, ValidatorInterface $validator, RouterInterface $router, Slugify $slugify)
    {
        $this->auth = $auth;
        $this->flash = $flash;
        $this->logger = $logger;
        $this->router = $router;
        $this->slugify = $slugify;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function allAction(Request $request, Response $response, array $args)
    {
        $places = Place::where('company_id', $this->auth->getCompanyId())
            ->orderBy('name', 'ASC')
            ->get();

        return $this->view->render($response, 'place/all.twig', [
            'places' => $places
        ]);
    }

    public function newAction(Request $request, Response $response, array $args)
    {
        $states = State::all();

        return $this->view->render($response, 'place/new.twig', [
            'states' => $states,
        ]);
    }

    public function editAction(Request $request, Response $response, array $args)
    {
        $place = Place::findOrFail($args['id']);

        $states = State::all();

        return $this->view->render($response, 'place/edit.twig', [
            'place' => $place,
            'states' => $states,
        ]);
    }

    public function saveAction(Request $request, Response $response, array $args)
    {
        $rules = [
            'name' => v::notEmpty(),
            'address' => v::notEmpty(),
            'city' => v::notEmpty(),
            'zip' => v::notEmpty(),
            'state' => v::notEmpty()->intVal()
        ];

        $validation = $this->validator->validate($request, $rules);

        if ($validation->failed()) {
            $this->flash->addMessage('danger', 'Su lugar no ha sido guardado, revise los errores en el formulario.');

            return $response->withRedirect($this->router->pathFor('place.new', [
                'lang' => $args['lang']
            ]));
        }

        $slug = filter_var($request->getParam('slug'), FILTER_SANITIZE_STRING);

        $shareable = $request->getParam('is_shareable');

        $place = Place::create([
            'name' => filter_var($request->getParam('name'), FILTER_SANITIZE_STRING),
            'slug' => $this->slugify->slugify(filter_var($request->getParam('name'), FILTER_SANITIZE_STRING)),
            'address' => filter_var($request->getParam('address'), FILTER_SANITIZE_STRING),
            'city' => filter_var($request->getParam('city'), FILTER_SANITIZE_STRING),
            'zip' => filter_var($request->getParam('zip'), FILTER_SANITIZE_STRING),
            'company_id' => $this->auth->getCompanyId(),
            'state_id' => filter_var($request->getParam('state'), FILTER_SANITIZE_NUMBER_INT),
            'is_shareable' => $shareable ? 1 : 0
        ]);

        return $response->withRedirect($this->router->pathFor('place.edit', [
            'lang' => $args['lang'],
            'id' => $place->place_id,
        ]));
    }

    public function modifyAction(Request $request, Response $response, array $args)
    {
        $rules = [
            'name' => v::notEmpty(),
            'address' => v::notEmpty(),
            'city' => v::notEmpty(),
            'zip' => v::notEmpty(),
            'state' => v::notEmpty()->intVal()
        ];

        $validation = $this->validator->validate($request, $rules);

        if ($validation->failed()) {
            $this->flash->addMessage('danger', 'Su lugar no ha sido guardado, revise los errores en el formulario.');

            return $response->withRedirect($this->router->pathFor('place.edit', [
                'lang' => $args['lang'],
                'id' => $args['id']
            ]));
        }

        $slug = filter_var($request->getParam('slug'), FILTER_SANITIZE_STRING);

        $shareable = $request->getParam('is_shareable');

        $place = Place::find($args['id']);

        $place->name = filter_var($request->getParam('name'), FILTER_SANITIZE_STRING);
        $place->slug = $this->slugify->slugify(filter_var($request->getParam('name'), FILTER_SANITIZE_STRING));
        $place->address = filter_var($request->getParam('address'), FILTER_SANITIZE_STRING);
        $place->city = filter_var($request->getParam('city'), FILTER_SANITIZE_STRING);
        $place->zip = filter_var($request->getParam('zip'), FILTER_SANITIZE_STRING);
        $place->company_id = $this->auth->getCompanyId();
        $place->state_id = filter_var($request->getParam('state'), FILTER_SANITIZE_NUMBER_INT);
        $place->is_shareable = $shareable ? 1 : 0;

        $place->save();

        return $response->withRedirect($this->router->pathFor('place.edit', [
            'lang' => $args['lang'],
            'id' => $place->place_id,
        ]));
    }
}
