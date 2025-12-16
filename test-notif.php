<?php
require_once 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;

echo "Ratchet est bien installé !\n";
var_dump(class_exists('Ratchet\\MessageComponentInterface')); // doit afficher bool(true)