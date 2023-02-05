<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Record;
use App\Entity\User;
use App\Repository\DoctorRepository;
use App\Repository\RecordRepository;
use App\Repository\UserRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class RecordController
{
    private DoctorRepository $doctorRepository;
    private UserRepository $userRepository;
    private RecordRepository $recordRepository;

    public function __construct(DoctorRepository $doctorRepository, UserRepository $userRepository, RecordRepository $recordRepository) {
        $this->doctorRepository = $doctorRepository;
        $this->userRepository = $userRepository;
        $this->recordRepository = $recordRepository;
    }
    public function recordToDoctor(Request $request, Response $response, $args)
    {
        //по хэдэрсу определяем текущего пользователя
        $token = $request->getHeader("Authorization");
        $token = reset($token); //достаю послежний элемент из массива

        //если нет токена, то сообщение - авторизуйтесь, вернуть респонс сразу "не указали токен"
        if (empty($token)) {
            $errors = "Не указали токен";
            $response->getBody()->write($errors);
            return $response->withStatus(401);
        }
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['token' => $token]);

        //когда указываю неправильный токен - должен получать ошибку "Не удалось авторизоваться"
        if (!$user instanceof User) {
            $errors = "Не удалось авторизоваться";
            $response->getBody()->write($errors);
            return $response->withStatus(401);
        }

        $data = json_decode($request->getBody()->getContents(), true);

        $errors = $this->validateRecordToDoctor($data);
        if (!empty($errors)) {
            $errors = json_encode($errors);
            $response->getBody()->write($errors);
            return $response;
        }
        $doctorId = $data['doctor_id'];
        $date = $data['date'];
        $time = $data['time'];

        /** @var Doctor $doctor */
        $doctor = $this->doctorRepository->findOneBy(['id' => $doctorId]);

        if (!$doctor instanceof Doctor) {
            $errors["doctor_id"] = "Такого доктора нет";
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode($errors));
            return $response;
        }

        $record = new Record($user, $doctor, $date, $time);

        $this->recordRepository->save($record, true);

        $record = $record->toArray();
        $response->getBody()->write(json_encode($record));
        return $response;
    }
    private function validateRecordToDoctor(array $data):array
    {
        $errors = [];

        if (empty($data['doctor_id'])) {
            $errors['doctor_id'] = "Не выбрали доктора";
        }
        if (empty($data['date'])) {
            $errors['date'] = "Не выбрали дату";
        }
        if (empty($data['time'])) {
            $errors['time'] = "Не выбрали время";
        }
        return $errors;
    }
}