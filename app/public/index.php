<?php

use App\Controller\DoctorController;
use App\Controller\RecordController;
use App\Controller\UserController;
use App\Controller\WeatherController;
use App\View\RegistrationView;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../config/bootstrap.php';

// Создаем приложение
AppFactory::setContainer($container);
$app = AppFactory::create();

// определяем routes
$app->post('/signUp', [UserController::class, "signUp"]);
$app->post('/signIn', [UserController::class, "signIn"]);
$app->put('/user', [UserController::class, "editUser"]);
$app->post('/doctor', [DoctorController::class, "addDoctor"]);
$app->get('/doctor', [DoctorController::class, "getDoctors"]);
$app->post('/record', [RecordController::class, "recordToDoctor"]);
$app->get('/weather', [WeatherController::class, "getWeather"]);
$app->get('/weathers', [WeatherController::class, "getWeathers"]);

//view
$app->get('/registration',[RegistrationView::class, "openRegistration"]);

$app->run();
