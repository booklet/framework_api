<?php
// Autoloader paths
$autoloader_paths_to_load = ['app/classes',
                             'app/controllers',
                             'app/helpers',
                             'app/models',
                             'app/polices',
                             'lib'];

include 'vendor/boooklet/framework/src/Autoloader.php';
foreach ($autoloader_paths_to_load as $path) {
    $loader = new Autoloader($path);
    spl_autoload_register([$loader, 'autoload']);
}

// Composer autoload
require_once('vendor/autoload.php');
