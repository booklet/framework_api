<?php
// Autoloader
// =============================================================================
require_once 'config/autoload.php';

try {
    // Application setup
    // =========================================================================
    require_once 'config/application.php';

    // Routing
    // =========================================================================
    require_once 'config/routing.php';

    if (!$match) {
        echo Response::raiseError(404, ['Resource not found.']);
        die();
    }

    // Connect to mysql database
    // TODO
    // $db_setup = 'db_'.Config::get('env');
    // MyDB::connect(Config::get($db_setup));

    // Run Controller#action
    if ($match) {
        // $match from 'app/core/router.php'
        // $match['target'] => UserController#index

        list($controller_name, $action_name) = explode('#', $match['target']);

        $params = Request::params($match['params']);
        $params['controller'] = $controller_name;
        $params['action'] = $action_name;

        $controller = new $controller_name($params);
        $body = $controller->$action_name();

        echo $body;
    }
} catch (Throwable $t) {
    if ($t->getCode() != 0) {
        $error_code = $t->getCode();
    } else {
        $error_code = 500;
    }

    echo Response::raiseError($error_code, [$t->getMessage()]);
}
