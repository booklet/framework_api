<?php
$router = new AltoRouter();
$router->setBasePath('');

// // Clients
// $router->map('GET','/clients', 'ClientsController#index', 'clients_path');
// $router->map('GET','/clients/[i:id]', 'ClientsController#show', 'client_path');
// $router->map('GET','/clients/new', 'ClientsController#new', 'new_client_path');
// $router->map('GET','/clients/[i:id]/edit', 'ClientsController#edit', 'edit_client_path');
// $router->map('POST','/clients', 'ClientsController#create', 'create_client_path');
// $router->map('PUT','/clients/[i:id]', 'ClientsController#update', 'update_client_path');

$router->map('GET','/', 'StaticPagesController#start', 'root_path');
Config::set('router', $router);

$method = $_SERVER['REQUEST_METHOD'];
if (isset($_POST) and isset($_POST['_method'])) {
    $method = $_POST['_method'];
}
// Match the current request
$match = $router->match(null, $method);
