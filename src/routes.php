<?php
// Routes

// Home
$app->get('[/]', 'HomeController:indexAction')->setName('home');

// Guest users routes
$app->group('/print', function () {
    // Sign In
    $this->get('/test', 'PrintController:testAction')->setName('print.test');
});
// Guest users routes
$app->group('/auth', function () {
    // Sign In
    $this->get('/signin', 'AuthController:getSignInAction')->setName('auth.signin');
    $this->post('/signin', 'AuthController:postSignInAction');
});
// ->add(new App\Middlewares\GuestMiddleware(
//     $container->auth,
//     $container->router
// ));

// Authenticated users routes
$app->group('', function () {
    // Auth SingOut and Changes
    $this->group('/auth', function () {
        // Sign Out
        $this->get('/signout', 'AuthController:getSignOutAction')->setName('auth.signout');
    });

    // Product Group
    $this->group('/product', function () {
        // All Products
        $this->get('/info/{id:[0-9]+}', 'ProductController:infoAction')->setName('product.info');
        $this->get('/{id:[0-9]+}/registrations/all', 'ProductController:InstaRegistrationsAction')->setName('product.registrations.all');
        $this->get('/{id:[0-9]+}/registrations/insta', 'ProductController:allRegistrationsAction')->setName('product.registrations.insta');
        $this->get('/{id:[0-9]+}/registrations/{type:[a-z0-9-]+}', 'ProductController:registrationsAction')->setName('product.registrations');
    });

    // Registration Group
    $this->group('/registration', function () {
        // Product QR Screen
        $this->get('/old/verification/product/{id:[0-9]+}', 'RegistrationController:productOldAction')->setName('registration.verification.old');
        $this->get('/verification/product/{id:[0-9]+}', 'RegistrationController:productAction')->setName('registration.verification');
        // Verify
        $this->get('/verify/{qr}', 'RegistrationController:verifyAction')->setName('registration.verify');
        // Toggle Verification
        $this->get('/toggle/verification/{qr:[a-zA-Z0-9_.-]+}', 'RegistrationController:toggleVerificationAction')->setName('registration.toggle.verification');
        // Create Registration
        $this->get('/create/product/{id:[0-9]+}', 'RegistrationController:createAction')->setName('registration.create');
        // Save New Registration
        $this->post('/save/product/{id:[0-9]+}', 'RegistrationController:saveAction')->setName('registration.save');
    });
})->add(new App\Middlewares\AuthenticatedMiddleware(
    $container->oauth,
    $container->flash,
    $container->router
));
