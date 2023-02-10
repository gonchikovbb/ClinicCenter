<?php

use App\Client\YandexClient;
use App\Controller\DoctorController;
use App\Controller\RecordController;
use App\Controller\UserController;
use App\Controller\WeatherController;
use App\Entity\Doctor;
use App\Entity\Record;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\DoctorRepository;
use App\Repository\RecordRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use UMA\DIC\Container;


return [
    UserController::class => function (Container $container) {

        /** @var EntityManager $em */
        $em = $container->get(EntityManager::class);

        /** @var UserRepository $userRepository */
        $userRepository = $em->getRepository(User::class);

        /** @var RoleRepository $roleRepository */
        $roleRepository = $em->getRepository(Role::class);

        return new UserController($userRepository, $roleRepository);
    },
    DoctorController::class => function (Container $container) {

        /** @var EntityManager $em */
        $em = $container->get(EntityManager::class);

        /** @var DoctorRepository $doctorRepository */
        $doctorRepository = $em->getRepository(Doctor::class);

        /** @var UserRepository $userRepository */
        $userRepository = $em->getRepository(User::class);

        /** @var RoleRepository $roleRepository */
        $roleRepository = $em->getRepository(Role::class);

        $connection = $em->getConnection();

        return new DoctorController($doctorRepository, $userRepository, $roleRepository, $connection);
    },
    RecordController::class => function (Container $container) {

        /** @var EntityManager $em */
        $em = $container->get(EntityManager::class);

        /** @var DoctorRepository $doctorRepository */
        $doctorRepository = $em->getRepository(Doctor::class);

        /** @var UserRepository $userRepository */
        $userRepository = $em->getRepository(User::class);

        /** @var RecordRepository $recordRepository */
        $recordRepository = $em->getRepository(Record::class);

        return new RecordController($doctorRepository, $userRepository, $recordRepository);
    },
    YandexClient::class => function(){
        return new YandexClient();
    },

    WeatherController::class => function (Container $container) {

        /** @var YandexClient $yandexClient */

        $yandexClient = $container->get(YandexClient::class);

        return new WeatherController($yandexClient);
    },

    EntityManager::class => static function (Container $c): EntityManager {

        /** @var array $settings */
        $settings = $c->get('settings');

        $cache = $settings['doctrine']['dev_mode'] ?
            DoctrineProvider::wrap(new ArrayAdapter()) :
            DoctrineProvider::wrap(new FilesystemAdapter(directory: $settings['doctrine']['cache_dir']));
        $config = Setup::createAttributeMetadataConfiguration(
            $settings['doctrine']['metadata_dirs'],
            $settings['doctrine']['dev_mode'],
            null,
            $cache
        );
        return EntityManager::create($settings['doctrine']['connection'], $config);
    }
];