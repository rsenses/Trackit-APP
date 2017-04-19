<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Slim\Interfaces\RouterInterface;
use Slim\Collection;

class BreadCrumbsMiddleware
{
    private $view;
    private $router;
    private $settings;

    public function __construct(Twig $view, RouterInterface $router, Collection $settings)
    {
        $this->view = $view;
        $this->router = $router;
        $this->settings = $settings;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $breadcrumbs = [
            'current' => 'Escritorio',
            'routes' => [[
                'name' => 'Escritorio',
                'route' => 'dashboard',
                'url' => $this->getUrl($request, 'dashboard')
            ]]
        ];

        //GET THE NAME OF CURRENT SELECTED PAGE
        $routeName = $request->getAttribute('route')->getName();

        //BUILD breadcrumbs
        switch($routeName) {
            case 'auth.change.password':
                $breadcrumbs['current'] = 'Cambiar Contraseña';
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'auth.edit':
                $breadcrumbs['current'] = 'Mis Datos';
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'product.all':
                $breadcrumbs['current'] = 'Eventos';
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'product.new':
                $breadcrumbs['current'] = 'Nuevo Evento';
                $breadcrumbs['routes'][] = [
                    'name' => 'Eventos',
                    'route' => 'product.all',
                    'url' => $this->getUrl($request, 'product.all')
                ];
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'product.edit':
                $breadcrumbs['current'] = 'Editar Evento';
                $breadcrumbs['routes'][] = [
                    'name' => 'Eventos',
                    'route' => 'product.all',
                    'url' => $this->getUrl($request, 'product.all')
                ];
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'registration.all':
                $breadcrumbs['current'] = 'Registros';
                $breadcrumbs['routes'][] = [
                    'name' => 'Eventos',
                    'route' => 'product.all',
                    'url' => $this->getUrl($request, 'product.all')
                ];
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'place.all':
                $breadcrumbs['current'] = 'Lugares';
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'place.new':
                $breadcrumbs['current'] = 'Nuevo Lugar';
                $breadcrumbs['routes'][] = [
                    'name' => 'Lugares',
                    'route' => 'place.all',
                    'url' => $this->getUrl($request, 'place.all')
                ];
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'place.edit':
                $breadcrumbs['current'] = 'Editar Lugar';
                $breadcrumbs['routes'][] = [
                    'name' => 'Lugares',
                    'route' => 'place.all',
                    'url' => $this->getUrl($request, 'place.all')
                ];
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'user.all':
                $breadcrumbs['current'] = 'Staff';
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
            case 'user.edit':
                $breadcrumbs['routes'][] = [
                    'name' => 'Staff',
                    'route' => 'user.all',
                    'url' => $this->getUrl($request, 'user.all')
                ];
                $breadcrumbs['current'] = 'Nuevo Usuario';
                $breadcrumbs['routes'][] = [
                    'name' => $breadcrumbs['current'],
                    'route' => $routeName,
                    'url' => $this->getUrl($request, $routeName)
                ];
                break;
        }

        //ALLOW VIEW TO USE IT
        $this->view->getEnvironment()->addGlobal('breadcrumbs', $breadcrumbs);
        $response = $next($request, $response);

        return $response;
    }

    private function getUrl(Request $request, $route) {
        $args = $request->getAttribute('routeInfo')[2] ?: ['lang' => $this->settings['allowedLocales'][0]];
        return $this->router->pathFor($route, $args);
    }
}
