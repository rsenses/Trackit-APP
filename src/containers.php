<?php

// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Guzzle HTTP
$container['guzzle'] = function ($container) {
    $settings = $container->get('settings')['api']['guzzle'];
    return new GuzzleHttp\Client($settings);
};

// Printer
$container['printer'] = function ($container) {
    $connector = new Mike42\Escpos\PrintConnectors\NetworkPrintConnector('192.168.1.220', 9100);
    return new Mike42\Escpos\Printer($connector);
};

// QRCode Generator
$container['qrcode'] = function ($container) {
    return new Endroid\QrCode\QrCode();
};

// GdEscposImage
$container['gdpos'] = function ($container) {
    return new Mike42\Escpos\GdEscposImage();
};

// Twig
$container['view'] = function ($container) {
    $settings = $container->get('settings')['view'];
    $view = new Slim\Views\Twig(
        $settings['template_path'],
        $settings['twig']
    );

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension(
        $container->get('router'),
        $container->get('request')->getUri()
    ));
    $view->addExtension(new Twig_Extension_Debug());

    // $view->getEnvironment()->addGlobal('auth', [
    //     'status' => $container->auth->getStatus(),
    //     'user' => $container->auth->getUserData()
    // ]);

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    if (!empty($_SESSION['products'])) {
        $view->getEnvironment()->addGlobal('products', $_SESSION['products']);
    }

    if (!empty($_SESSION['product'])) {
        $view->getEnvironment()->addGlobal('product', $_SESSION['product']);
    }

    return $view;
};

// Flash messages
$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages();
};

// Validation
$container['validator'] = function ($container) {
    return new \App\Validation\Validator();
};

// Slugify
$container['slugify'] = function ($container) {
    return new Cocur\Slugify\Slugify();
};

// Eloquent
// $capsule = new \Illuminate\Database\Capsule\Manager();
// $capsule->addConnection($container->get('settings')['db']);
// $capsule->setAsGlobal();
// $capsule->bootEloquent();

// $container['db'] = function ($container) use ($capsule) {
//     return $capsule;
// };

// Language Negotiator
// $container['negotiator'] = function ($container) {
//     return new Negotiation\LanguageNegotiator();
// };

// CSRF
$container['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard();
};

// Monolog
$container['logger'] = function ($container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
