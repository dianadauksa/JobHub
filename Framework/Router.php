<?php

namespace Framework;

use App\Controllers\ErrorController;

class Router {
    protected $routes = [];

    /**
     * Register a route to the router
     * 
     * @param string $method
     * @param string $uri
     * @param string $action
     * @return void
     */
    public function register_route($method, $uri, $action): void {
        list($controller, $controller_method) = explode('@', $action);
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controller_method' => $controller_method
        ];
    }

    /**
     * Add a GET route to the router
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function get($uri, $controller): void {
        $this->register_route('GET', $uri, $controller);
    }

    /**
     * Add a POST route to the router
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post($uri, $controller): void {
        $this->register_route('POST', $uri, $controller);
    }

    /**
     * Add a PUT route to the router
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put($uri, $controller): void {
        $this->register_route('PUT', $uri, $controller);
    }

    /**
     * Add a DELETE route to the router
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete($uri, $controller): void {
        $this->register_route('DELETE', $uri, $controller);
    }

    /**
     * Route the request to the appropriate controller
     * 
     * @param string $uri
     * @return void
     */
    public function route($uri): void {
        $request_method = $_SERVER['REQUEST_METHOD'];

        if ($request_method === 'POST' && isset($_POST['_method'])) {
            $request_method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            $uri_segments = explode('/', trim($uri, '/'));
            $route_segments = explode('/', trim($route['uri'], '/'));
            $match = true;

            if (count($uri_segments) === count($route_segments) && strtoupper($route['method']) === $request_method) {
                $params = [];
                $match = true;
                for ($i = 0; $i < count($uri_segments); $i++) {
                    if ($route_segments[$i] !== $uri_segments[$i]
                        && !preg_match('/\{(.+?)\}/', $route_segments[$i])) {
                        $match = false;
                        break;
                    }

                    if (preg_match('/\{(.+?)\}/', $route_segments[$i], $matches)) {
                        $params[$matches[1]] = $uri_segments[$i];
                    }
                }

                if ($match) {
                    $controller = 'App\\Controllers\\'.$route['controller'];
                    $controller_method = $route['controller_method'];

                    $controller_instance = new $controller();
                    $controller_instance->$controller_method($params);

                    return;
                }
            }
        }

        ErrorController::not_found();
    }
}
            