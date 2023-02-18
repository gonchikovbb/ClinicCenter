<?php

use App\Controller\DoctorController;
use App\Controller\RecordController;
use App\Controller\UserController;
use App\Controller\WeatherController;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Slim\Factory\AppFactory;
use UMA\DIC\Container;


require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/dependencies.php';

$container = require __DIR__ . '/../config/bootstrap.php';

// Set container to create App with on AppFactory
AppFactory::setContainer($container);
$app = AppFactory::create();

// Define app routes
$app->post('/signUp', [UserController::class, "signUp"]);
$app->post('/signIn', [UserController::class, "signIn"]);
$app->put('/user', [UserController::class, "editUser"]);
$app->post('/doctor', [DoctorController::class, "addDoctor"]);
$app->get('/doctor', [DoctorController::class, "getDoctors"]);
$app->post('/record', [RecordController::class, "recordToDoctor"]);
$app->get('/weather', [WeatherController::class, "getWeather"]);
$app->get('/weathers', [WeatherController::class, "getWeathers"]);

$app->run();
