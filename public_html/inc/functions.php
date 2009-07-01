<?php
/* Require some stuff. */
require_once 'functions/db.php';
require_once 'functions/html.php';
require_once 'functions/standard_lib.php';

/**
 * Autoloads our classes.
 */
function __autoload($class) {
        $class_file = 'inc/classes/' . trim($class) . '.php';
        if(true == file_exists($class_file)) {
                require_once $class_file;
        }
}
?>