<?php


use UMA\DIC\Container;

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/settings.php';
$dependencies = require __DIR__ . '/dependencies.php';
$settings = array_merge($settings, $dependencies);

$container = new Container($settings);

return $container;
