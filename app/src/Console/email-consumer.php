<?php

use App\Service\MailerService;
use App\Service\QueueService;
use UMA\DIC\Container;

require __DIR__ . '/../../vendor/autoload.php';

/** @var Container $container */
$container = require __DIR__ . '/../../config/bootstrap.php';

/** @var QueueService $queueService */
$queueService = $container->get(QueueService::class);

/** @var MailerService $mailerService */
$mailerService = $container->get(MailerService::class);

$callback = function ($msg) use ($mailerService) {

    $email = json_decode(($msg->body),true)['email'];

    echo ' [x] Receiver ', $email, "\n";

    $mailerService->sendMessage(
        $email,
        'Поздравляю! Вы зарегистрированы!',
        'Теперь вы можете записываться на приём к доктору!'
    );

};

echo" [*] Waiting for messages. To exit press CTRL+C\n";

$queueService->consume('email', $callback);
