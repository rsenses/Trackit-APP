<?php
// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container['HomeController'] =  function ($container) {
    return new App\Controllers\HomeController(
        $container->flash,
        $container->logger,
        $container->router,
        $container->view
    );
};

$container['AuthController'] =  function ($container) {
    return new App\Controllers\Auth\AuthController(
        $container->view,
        $container->logger,
        $container->flash,
        $container->validator,
        $container->oauth,
        $container->router,
        $container->guzzle
    );
};

$container['PrintController'] =  function ($container) {
    return new App\Controllers\PrintController(
        $container->view,
        $container->logger,
        $container->flash,
        $container->validator,
        $container->oauth,
        $container->router,
        $container->guzzle,
        $container->printer,
        $container->qrcode,
        $container->gdpos
    );
};

$container['ProductController'] =  function ($container) {
    return new App\Controllers\ProductController(
        $container->view,
        $container->logger,
        $container->oauth,
        $container->flash,
        $container->router,
        $container->guzzle,
        $container->get('settings')
    );
};

$container['RegistrationController'] =  function ($container) {
    return new App\Controllers\RegistrationController(
        $container->view,
        $container->logger,
        $container->oauth,
        $container->flash,
        $container->guzzle,
        $container->router,
        $container->csrf,
        $container->validator,
        $container->get('settings')
    );
};
