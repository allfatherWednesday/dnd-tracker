<?php

namespace app;

use app\Controllers\Controller;
use app\Models\ConfModel;
use Exception;


class Router
{
    private $routes = [
        '' => 'HomeController::home',
        'api' => 'ApiController::apiManager',
        'login' => 'CharacterController::login',
        'logout' => 'CharacterController::logout',
        'admin' => 'AdminController::adminLogin',
		'map' => 'MapController::displayMap',
		'add-object' => 'MapObjectController::addObject',
        'edit' => 'CharacterController::edit',
        '404' => 'Controller::error404',
    ];
    private $publicRoutes = ['', 'logout', 'admin'];

    public function renderController(string $request)
    {
        $parameters = explode('/',$request);
        $route = $parameters[0];

        $controller = new Controller();
        if (CONF['allow_login']['conf_value'] !== '1' && !in_array($route, $this->publicRoutes)) {
            $controller->redirect('logout');
        }
        $routeStr = $this->getRoute($route);
        if ($routeStr) {
            $request = explode('::', $routeStr);
            [$controllerName, $method] = $request;

            unset($parameters[0], $_GET['r']);
            $parameters = array_values($parameters);

            try {
                $controller = "app\\Controllers\\$controllerName";
                $currentController = new $controller();
                $currentController->$method($parameters);
            } catch (Exception $e) {
                throw new Exception($e);
            }

        } else {
            $controller->redirect('404');
        }
        return 'done';
    }

    public function getRoute($route)
    {
        if (key_exists($route,$this->routes)) {
            return $this->routes[$route];
        }

        return;
    }

    public function globalVars()
    {
        define('HOST', $_ENV['HOST']);
        $confModel = new ConfModel();
        define('CONF', $confModel->getConf());
    }

}