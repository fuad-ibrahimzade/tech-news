<?php

namespace Core;

class Router
{

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    protected $routes = [];
    protected $params = [];

    public function add($route, $params = [])
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);
                $action = $this->params['action'];
                $methods = is_array($this->params['methods']) ? $this->params['methods']:[$this->params['methods']];

//                if (preg_match('/action$/i', $action) == 0) {
//                    $controller_object->$action();
//
//                }
                $methodsMath= (count($methods)==1 && strtolower($methods[0])=='any') ? true:false;
                foreach ($methods as $method) {
                    if($_SERVER['REQUEST_METHOD'] ==strtoupper($method)){
                        $methodsMath=true;
                        break;
                    }
                }
                if(!$methodsMath){
                    echo "Now Allowed";
                }
                else if(is_callable([$controller_object, $action])) {
//                    if (isset($this->params['id'])) {
//                        $controller_object->$action($this->params['id']);
//                    } else {
//                        $controller_object->$action();
//                    }
                    $rawData = file_get_contents("php://input");
                    $validJSON=json_decode($rawData)!==null;
                    if($_SERVER['REQUEST_METHOD'] == 'GET' && !$validJSON){
                        $controller_object->$action($_GET);
                    }
                    else if($_SERVER['REQUEST_METHOD'] == 'POST' && !$validJSON){
                        $controller_object->$action($_POST);
                    }
                    else if($validJSON){
                        $controller_object->$action(json_decode($rawData));
                    }
                } else {
                    echo "alinmadi amma conroller var. action suffixsiz yoxla" ;
                }
            } else {
                echo "Controller yoxdur";
            }
        } else {
            echo 'route alinmadi controller yoxdu';
        }
    }

    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }

    public function redirrectURL($redirrect_path)
    {
        $request_url = apache_getenv("HTTP_HOST") . apache_getenv("REQUEST_URI");
        $isBaseUrl=$_SERVER['SERVER_NAME'].'/'===$request_url;
        if(!$isBaseUrl){
            header('Location: '.$redirrect_path);
        }
    }

    public function currentURL(){
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }


}
