<?php
session_start();

// AUTOLOADER PARFAIT pour ton architecture App\...
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/../app/' . substr($class, 4) . '.php';
        $file = str_replace('\\', '/', $file);
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
require_once '../vendor/autoload.php';
// Charger le Router
require_once __DIR__ . '/../app/Core/Router.php';

use App\Core\Router;

$router = new Router();
$router->run();
