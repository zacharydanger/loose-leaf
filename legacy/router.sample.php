<?php
//MAKE SURE THAT loose_leaf.php is included/required somewheres before now.
$route = Router::route(get_var('url'));
$D = new Dispatcher($route['controller'], $route['action'], $route['id']);
$D->setUser($USER);
$D->dispatch();
?>
