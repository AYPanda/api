<?php

namespace classes;


class Router
{

    private $uri;

    private $controller;
    private $action;
    private $params;

    public function __construct($uri)
    {
        if ($uri) {
            $uri = str_replace("/api", "", $uri);
            $this->uri = $uri;
            $this->getRoute();
        }
    }

    private function getRoute()
    {
        $this->parseRoute();
        $class = "\\controllers\\" . $this->controller . 'Controller';
        $action = $this->action;
        if (class_exists($class)) {
            $controller = new $class();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                echo "Метод не найден";
            }
        } else {
            echo "Класс не найден";
        }
    }

    private function parseRoute()
    {
        $uri = explode("/", $this->uri);
        $this->controller = $uri[1] != null ? ucfirst($uri[1]) : "Index";
        $this->action = $uri[2] != null ? ucfirst($uri[2]) : "Index";
        $this->params = $uri[3];
    }
}