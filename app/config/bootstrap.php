<?php

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use UMA\DIC\Container;

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/settings.php';
$dependencies = require __DIR__ . '/dependencies.php';
$settings = array_merge($settings, $dependencies);

$container = new Container($settings);

return $container;

//    public function signUp(Request $request, Response $response, $args)
//    {
//        $dbh = new PDO("pgsql:host=db;port=5432;dbname=postgres;", 'user', 'pass');
//        //insert добавляю в бд
//        $data = json_decode($request->getBody()->getContents(),true);
//        $sql = "INSERT INTO users (first_name, second_name, gender, third_name, email, phone, role_id) VALUES (:first_name, :second_name, :gender, :third_name, :email, :phone, :role_id)";
//        $dbh->prepare($sql)->execute($data);
//
//        $stmt = $dbh->query("SELECT * FROM  \"users\" WHERE \"email\"='{$data['email']}'");
//        //print_r($stmt->fetch());
//        $result = json_encode($stmt->fetch());
//        $response->getBody()->write($result);
//        return $response;