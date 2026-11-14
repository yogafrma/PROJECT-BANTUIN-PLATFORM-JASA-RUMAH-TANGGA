<?php
/**
 * Base Controller Class
 * Project: Teknisi Rumah Tangga
 */

abstract class Controller {
    /**
     * Load Model
     *
     * @param string $model Name of model class
     * @return object Instance of model
     */
    public function model($model) {
        $modelPath = file_exists(__DIR__ . '/../models/' . $model . '.php') 
            ? __DIR__ . '/../models/' . $model . '.php' 
            : '../app/models/' . $model . '.php';

        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        }
        die("Model {$model} tidak ditemukan.");
    }

    /**
     * Render View with Layout
     *
     * @param string $view Path to view file relative to views folder (e.g. 'home/index')
     * @param array $data Data to be passed to view
     * @param bool $withLayout Whether to wrap view in header and footer
     * @return void
     */
    public function view($view, $data = [], $withLayout = true) {
        // Extract array keys into variables for easy access in view templates
        extract($data);

        $baseViewDir = file_exists(__DIR__ . '/../views/') ? __DIR__ . '/../views/' : '../app/views/';
        $viewPath = $baseViewDir . $view . '.php';
        $headerPath = $baseViewDir . 'layouts/header.php';
        $footerPath = $baseViewDir . 'layouts/footer.php';

        if (file_exists($viewPath)) {
            if ($withLayout && file_exists($headerPath)) {
                require_once $headerPath;
            }

            require_once $viewPath;

            if ($withLayout && file_exists($footerPath)) {
                require_once $footerPath;
            }
        } else {
            die("View {$view} tidak ditemukan di lokasi: {$viewPath}");
        }
    }

    /**
     * Return JSON Response
     *
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    public function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
