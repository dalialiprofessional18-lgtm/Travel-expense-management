<?php

$routes = [];

foreach (glob(__DIR__ . "/*.php") as $file) {
    if (basename($file) !== "load.php") {
        $routes = array_merge($routes, require $file);
    }
}

return $routes;
