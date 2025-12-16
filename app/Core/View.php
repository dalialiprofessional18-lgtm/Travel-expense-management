<?php
namespace App\Core;
// helper to render partials
function view($file, $data = [])
{
    extract($data);
    require __DIR__ . '/../Views/' . $file;
}
