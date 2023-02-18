<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../../vendor/autoload.php';

$connection = new AMQPStreamConnection('rabbitmq','5672','myuser','mypassword');
$channel = $connection->channel();

$channel->queue_declare('hello',false,false,false,false);

echo" [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Receiver ', $msg->body, "\n";
    //PHPMailer Object``
//    $mail = new PHPMailer(true); //Argument true in constructor enables exceptions`
//    $mail->From = "doctor@ya.ru";
//    $mail->FromName = "Admin";
//    $mail->addAddress("gonbb@yandex.ru");
//    $mail->Subject = "Succesful!";
//    $mail->Body = "<i>Hi~</i>";
//    $mail->AltBody = "This is the plain text version of the email content";
//    try {
//        $mail->send();
//        echo "Message has been sent successfully";
//    } catch (Exception $e) {
//        echo "Mailer Error: " . $mail->ErrorInfo;
//    }

    //PHPMailer
//        $mail = new PHPMailer;
//        $mail->CharSet = 'UTF-8';
//        $mail->setFrom('Server');
//        $mail->addAddress('gonchikovbb@gmail.com');
//        $mail->Subject = 'Succesful!';
//        $mail->msgHTML('<p>test</p>');
//
//        $mail->send();
//        if ($mail->send()) {
//            echo 'Succses';
//        } else {
//            echo 'Fail: ' .$mail->ErrorInfo;
//        }
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 2;         /*Оставляем как есть*/
        $mail->isSMTP();              /*Запускаем настройку SMTP*/
        $mail->Host = 'smtp.mail.ru'; /*Выбираем сервер SMTP*/
        $mail->SMTPAuth = true;        /*Активируем авторизацию на своей почте*/
        $mail->Username = 'gonchikov_bb@mail.ru';   /*Имя(логин) от аккаунта почты отправителя */
        $mail->Password = 'HRquyFXWhLLwth8Yzub7';        /*Пароль от аккаунта  почты отправителя */
        $mail->SMTPSecure = 'ssl';            /*Указываем протокол*/
        $mail->Port = 465;			/*Указываем порт*/
        $mail->CharSet = 'UTF-8';/*Выставляем кодировку*/


        $mail->setFrom('gonchikov_bb@mail.ru');/*Указываем адрес почты отправителя */
        /*Указываем перечень адресов почты куда отсылаем сообщение*/
        $mail->addAddress('gonchikovbb@gmail.com');

        /*Указываем вложения, здесь к примеру я из корневой директории
        отправляю по почте файл test.html*/
        //$mail->addAttachment(__DIR__.'/test.html','test.html');

        $mail->isHTML(true);      /*формируем html сообщение*/
        $mail->Subject = 'Поздравляю! Вы зарегистрированы!'; /*Заголовок сообщения*/
        $mail->Body    = 'Теперь вы можете записываться на приём к доктору!';/* Текст сообщения */
        $mail->send();
        echo 'Сообщение успешно отправлено';

    } catch (Exception $e) {
        echo 'При отправке сообщения произошла следующая ошибка : ', $mail->ErrorInfo;
    }



};

$channel->basic_consume('hello','',false,true,false,false, $callback);



while ($channel->is_open()) {
    $channel->wait();
}