<?php

/**
 * App.php — Core Router
 * 
 * Parses the URL and dispatches to the correct Controller + method.
 * URL format: /controller/method/param1/param2/...
 */
class App {
    protected mixed $controller = DEFAULT_CONTROLLER;
    protected string $method     = DEFAULT_METHOD;
    protected array  $params     = [];

    public function __construct() {
        $url = $this->parseUrl();

        // --- Resolve Controller ---
        if (!empty($url[0])) {
            $controllerName = ucfirst(strtolower($url[0]));
            $controllerFile = APPROOT . '/controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            } else {
                $this->notFound();
                return;
            }
        }

        require_once APPROOT . '/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // --- Resolve Method ---
        if (!empty($url[1])) {
            $methodName = strtolower($url[1]);
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            } else {
                $this->notFound();
                return;
            }
        }

        // --- Resolve Params ---
        $this->params = !empty($url) ? array_values($url) : [];

        // --- Dispatch ---
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse the REQUEST_URI into an array of segments.
     */
    private function parseUrl(): array {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }

    /**
     * Simple 404 handler.
     */
    private function notFound(): void {
        http_response_code(404);
        require_once APPROOT . '/views/templates/404.php';
    }
}
