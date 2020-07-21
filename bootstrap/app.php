<?php

define('ROOT', __DIR__ . '/..');
define('APP_DIR', ROOT . '/app');
define('PUBLIC_DIR', ROOT . '/public');
define('CONTROLLERS_DIR', APP_DIR . '/Controllers');
define('VIEWS_DIR', APP_DIR . '/Views');

$router = new \App\Core\Router();
$router->initHandler();

if(!$router->isFoundRoute())
    die;

$controller_name = $router->getController();
$action_name = $router->getAction();
$vars = $router->getVars();

$controller = new $controller_name();
$controller->$action_name(...$vars);