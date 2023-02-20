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
use App\Entity\Weather;
use App\Repository\DoctorRepository;
use App\Repository\RecordRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\WeatherRepository;
use App\Service\MailerService;
use App\Service\QueueService;
use App\Service\WeatherService;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PHPMailer\PHPMailer\PHPMailer;
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

        $queueService = $container->get(QueueService::class);

        return new UserController($userRepository, $roleRepository, $queueService);
    },

    QueueService::class => function () {
        return new QueueService();
    },

    PHPMailer::class => function (Container $c) {

        /** @var array $settings */
        $settings = $c->get('settings');

        $mail = new PhpMailer();
        $mail->SMTPDebug = $settings['mailer']['SMTPDebug'];
        $mail->SMTPAuth = $settings['mailer']['SMTPAuth'];
        $mail->SMTPSecure = $settings['mailer']['SMTPSecure'];
        $mail->Port = $settings['mailer']['Port'];
        $mail->CharSet = $settings['mailer']['CharSet'];
        $mail->Host = $settings['mailer']['Host'];
        $mail->Username = $settings['mailer']['Username'];
        $mail->Password = $settings['mailer']['Password'];
        $mail->isSMTP();
        $mail->setFrom($mail->Username);
        $mail->isHTML(true);

        return $mail;
    },

    MailerService::class => function (Container $container) {
        $phpMailer = $container->get(PHPMailer::class);
        return new MailerService($phpMailer);
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
        /** @var EntityManager $em */
        $em = $container->get(EntityManager::class);

        $weatherService = $container->get(WeatherService::class);

        $connection = $em->getConnection();

        return new WeatherController($connection, $weatherService);
    },

    WeatherService::class =>function (Container $container) {
        /** @var EntityManager $em */
        $em = $container->get(EntityManager::class);

        /** @var WeatherRepository $weatherRepository */
        $weatherRepository = $em->getRepository(Weather::class);

        /** @var YandexClient $yandexClient */
        $yandexClient = $container->get(YandexClient::class);

        return new WeatherService($yandexClient,$weatherRepository);
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