<?php

/**
 * Routing Definitions
 */

require __DIR__.'/middleware.php';

$controller = new \MiniFace\Controller\MainController($app);


$factory = $controller->connect($app)
    ->before(checkPostContent());

$app->mount('', $factory);

