<?php

/*
 * Registers providers needed including our own
 */

$app->register(new \Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../src/Resources/views'
]);

$app->register(new \Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/log/dev.log',
));

