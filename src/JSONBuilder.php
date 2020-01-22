<?php
class JSONBuilder
{
    public $data;
    public $controller_and_action_or_path;

    /**
    * Ta klasa uzywa pliku widoku do budowy obiektu odpowiedzi
    */
    public function __construct($data, $controller_and_action_or_path)
    {
        $this->data = $data;
        $this->controller_and_action_or_path = $controller_and_action_or_path;
    }

    public function render()
    {
        $view_file_path = $this->getFilePath();

        if (file_exists($view_file_path)) {
            $data = $this->data; // $data variable use in view
            include $view_file_path; // Grab $response variable
        } else {
            throw new RuntimeException('Missing view file.');
        }

        return $response;
    }

    private function getFilePath()
    {
        // Check if template is a path or ClassController::action
        if (strpos($this->controller_and_action_or_path, '::') !== false) {
            // SessionsController::index => $class = 'session', $method = 'index'
            list($controller, $method) = explode('::', $this->controller_and_action_or_path);
            $class = str_replace('Controller', '', $controller);
            $class_pluralize_name = Inflector::pluralize($class);
            $class = StringUntils::camelCaseToUnderscore($class_pluralize_name);

            // Check first module view folder, then default view folder
            $module_name = $this->getModuleNameBaseOnControllerName($controller);
            $module_file_path = 'app/modules/' . $module_name. '/views/' . $class . '/' . $method . '.php';
            if (file_exists($module_file_path)) {
                return $module_file_path;
            } else {
                return 'app/views/' . $class . '/' . $method . '.php';
            }
        }

        return $this->controller_and_action_or_path;
    }

    private function getModuleNameBaseOnControllerName($controller)
    {
        $reflector = new ReflectionClass($controller);
        $class_file_name = $reflector->getFileName();
        $path_to_class_file_name = dirname($class_file_name);

        // "/Users/admin/Sites/api.booklet.pl/app/modules/client/controllers" => "client"
        $path_elements = explode('/', $path_to_class_file_name);
        $module_name = $path_elements[count($path_elements)-2];

        return $module_name;
    }
}
