<?php
//let's configure stuff.
define('DB_HOST', 'localhost', false);
define('DB_USER', 'user', false);
define('DB_PASS', 'pass', false);
define('DB_NAME', 'db', false);

//path to the root of the web app
define('DIR_ROOT', '/path/to/vhost/public_html/', false);

//where the change scripts used to create the database are located
define('DIR_SCHEMA', dirname(DIR_ROOT) . '/data/', false);

//where can people find this particular install of the site?
//DO NOT add a trailing slash (http://1kb.dev instead of http://1kb.dev/)
define('SITE_URL', 'http://foobar.foo', true);
?>