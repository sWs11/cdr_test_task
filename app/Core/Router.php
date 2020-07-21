<?php

namespace App\Core;

use FastRoute\Dispatcher;

class Router {

    /**
     * @var Dispatcher $dispatcher
     */
    private $dispatcher;

    /**
     * @var $handler
     */
    private $handler;

    /**
     * @var $controller
     */
    private $controller;

    /**
     * @var $action
     */
    private $action;

    /**
     * @var $vars
     */
    private $vars;

    /**
     * @var bool $found_route
     */
    private $found_route = false;


    public function __construct() {
        $this->dispatcher = require_once ROOT . '/config/routes.php';
    }

    public function initHandler() {
        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found

                header('NOT_FOUND', true, 404);

                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                break;
            case \FastRoute\Dispatcher::FOUND:
                $this->found_route = true;

                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $this->handler = $handler;
                $this->vars = $vars;

                $this->parseHandler();
//
                break;
        }
    }

    private function parseHandler() {
        $handler_array = explode('@', $this->handler);
        $controller = array_shift($handler_array);
        $action = array_shift($handler_array);

        $this->controller = $controller;
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return mixed
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return bool
     */
    public function isFoundRoute()
    {
        return $this->found_route;
    }
}