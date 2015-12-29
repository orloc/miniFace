<?php

/*
 * Registers providers needed including our own
 */


$config = require_once __DIR__.'/config.php';

$app->register(new \Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../src/Resources/views'
]);

$app->register(new \Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__.'/log/dev.log',
]);

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => $config['db']
]);