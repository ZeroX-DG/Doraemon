<?php
// Require composer autoloader
require __DIR__ . '/vendor/autoload.php';
// database setting
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'doraemon',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_vietnamese_ci',
    'prefix'    => '',
]);
// Set the event dispatcher used by Eloquent models
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
$capsule->setEventDispatcher(new Dispatcher(new Container));
// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();
// Setup the Eloquent ORM
$capsule->bootEloquent();
// constants
require 'libs/constants.php';
// Require view class
$GLOBALS['m'] = new Mustache_Engine(
    array(
        'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views'),
        'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views' . '/partials'),
        'escape' => function($value) {
            return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
        },
        'helpers' => [
            "view_path" => VIEW_FOLDER
        ]
    )
);
// Require helpers
require 'libs/Helpers.php';
// Require models
foreach (glob("models/*.php") as $filename)
{
    require $filename;
}
// Require controllers
foreach (glob("controllers/*.php") as $filename)
{
    require $filename;
}
// Create Router instance
$router = new \Bramus\Router\Router();
// Route list
require 'libs/routes.php';
// Run it!
$router->run();