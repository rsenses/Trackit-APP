<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';

// Errores en archivo log o en pantalla si estamos en desarrollo
error_reporting(-1);
// Errores en archivo log o en pantalla si estamos en desarrollo
ini_set('ignore_repeated_source', 0);
ini_set('ignore_repeated_errors', 1); // do not log repeating errors
// source of error plays role in determining if errors are different
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/../storage/logs/'.date('Y-m-d').'_error.log');
if ($settings['settings']['displayErrorDetails']) {
    ini_set('display_errors', 1); // Mostramos los errores en pantalla
    ini_set('display_startup_errors', 1);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// Set up session
require __DIR__ . '/../src/session.php';

// START SLIM 3
$container = new \Slim\Container($settings);
$app = new \Slim\App($container);

// Set up containers
require __DIR__ . '/../src/containers.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Register middleware
require __DIR__ . '/../src/middlewares.php';

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Custom Validators
require __DIR__ . '/../src/validators.php';

// Run app
$app->run();
