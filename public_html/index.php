<?php
require_once 'inc/global.php';
$route = Router::route(get_var('url'));
$D = new Dispatcher($route['controller'], $route['action'], $route['id']);
$D->setUser($USER);
$D->dispatch();
?>
