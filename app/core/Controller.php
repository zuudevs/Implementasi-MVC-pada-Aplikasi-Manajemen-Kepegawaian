<?php

/**
 * Controller.php — Base Controller
 * 
 * All controllers extend this class.
 * Provides helpers to load models and render views.
 */
class Controller {

    /**
     * Load and instantiate a model.
     *
     * @param  string $model  The model class name (without .php)
     * @return object         An instance of the requested model
     */
    public function model(string $model): object {
        $file = APPROOT . '/models/' . $model . '.php';

        if (!file_exists($file)) {
            die("Model '{$model}' tidak ditemukan.");
        }

        require_once $file;
        return new $model();
    }

    /**
     * Render a view file, passing data as extracted variables.
     *
     * @param  string $view   Path relative to app/views/ (e.g. 'employee/index')
     * @param  array  $data   Associative array of variables to pass to the view
     */
    public function view(string $view, array $data = []): void {
        $file = APPROOT . '/views/' . $view . '.php';

        if (!file_exists($file)) {
            die("View '{$view}' tidak ditemukan.");
        }

        // Extract data array as individual variables inside the view scope
        extract($data);

        require_once $file;
    }
}
