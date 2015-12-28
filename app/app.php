<?php

/**
 * Pulls various parts of the application
 */

require_once __DIR__.'/bootstrap.php';

$app = new Silex\Application();

$app['debug'] = getenv('APP_ENV') === 'dev' ? true : false;

require __DIR__.'/providers.php';
require __DIR__.'/routes.php';

return $app;



