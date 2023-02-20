<?php
namespace App\Controller;
use App\Entity\Doctor;
use App\Entity\User;
use App\Repository\DoctorRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class DoctorController
{
    private DoctorRepository $doctorRepository;
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private Connection $connection;

    public function __construct(
        DoctorRepository $doctorRepository,
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        Connection $connection
    ){
        $this->doctorRepository = $doctorRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->connection = $connection;
    }

    public function addDoctor(Request $request, Response $response, $args)
    {
        //Проверка прав пользователя, является ли админом
        //по хэдэрсу определяем текущего пользователя
        $token = $request->getHeader("Authorization");
        $token = reset($token); //достаю послежний элемент из массива

        //если нет токена, то сообщение - авторизуйтесь, вернуть респонс сразу "не указали токен"
        if (empty($token)) {
            $errors = "Не указали токен";
            $response->getBody()->write($errors);
            return $response->withStatus(401);
        }

        $user = $this->userRepository->findOneBy(['token' => $token]);

        //когда указываю неправильный токен - должен получать ошибку "Не удалось авторизоваться"
        if (!$user instanceof User) {
            $errors = "Не удалось авторизоваться";
            $response->getBody()->write($errors);
            return $response->withStatus(401);
        }

        //по текущему пользователю определяем его роль, является ли он админом
        if ($user->getRole()->getName() !== "admin") {
            $errors = "Нет прав";
            $response->getBody()->write($errors);
            return $response->withStatus(401);
        }

        $data = json_decode($request->getBody()->getContents(), true);

        $errors = $this->validateAddDoctor($data);

        if (!empty($errors)) {
            $errors = json_encode($errors);
            $response->getBody()->write($errors);
            return $response;
        }

        $firstName = $data['first_name'];
        $secondName = $data['second_name'];
        $gender = $data['gender'];
        $email = $data['email'];
        $password = $data['password'];
        $phone = $data['phone'];
        $thirdName = $data['third_name'] ?? null;
        $role = $this->roleRepository->findOneBy(['name' => 'doctor']);

        $password = password_hash($password, PASSWORD_DEFAULT);

        $this->connection->beginTransaction();

        try {
            //addUser
            $user = new User(
                $firstName,
                $secondName,
                $gender,
                $email,
                $password,
                $phone,
                $role,
                $thirdName
            );

            $this->userRepository->save($user, true);

            //addDoctor
            $specialization = $data['specialization'];
            $experience = $data['experience'];
            $education = $data['education'];
            $clinics = $data['clinics'] ?? null;
            $instagram = $data['instagram'] ?? null;
            $vk = $data['vk'] ?? null;

            $doctor = new Doctor(
                $user,
                $specialization,
                $experience,
                $education,
                $clinics,
                $instagram,
                $vk
            );

            $this->doctorRepository->save($doctor, true);

            $this->connection->commit();

            $doctor = $doctor->toArray();
            $response->getBody()->write(json_encode($doctor));

        } catch (Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
        return $response;
    }

    private function validateAddDoctor(array $data):array
    {
        $errors = [];

        if (empty($data['specialization'])) {
            $errors["specialization"] = "Введите специальность";
        } elseif (mb_strlen($data['specialization']) > 50) {
            $errors["specialization"] = "Специальность не должна быть больше 50 символов";
        }

        if (empty($data['experience'])) {
            $errors["experience"] = "Введите стаж";
        } elseif (mb_strlen($data['experience']) > 30) {
            $errors["experience"] = "Стаж не должен быть больше 30 символов";
        }

        if (empty($data['education'])) {
            $errors["education"] = "Введите образование";
        } elseif (mb_strlen($data['education']) > 100) {
            $errors["education"] = "Образование не должно быть больше 100 символов";
        }

        if (!empty($data['clinics']) && (mb_strlen($data['clinics']) > 100)) {
            $errors["clinics"] = "Место работы не должно быть больше 100 символов";
        }

        if (!empty($data['instagram']) && (mb_strlen($data['instagram']) > 30)) {
            $errors["instagram"] = "Инстаграм не доджен быть больше 30 символов";
        }

        if (!empty($data['vk']) && (mb_strlen($data['vk']) > 30)) {
            $errors["vk"] = "vk не доджен быть больше 30 символов";
        }
        return $errors;
    }

    public function getDoctors(Request $request, Response $response, $args)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $errors = $this->validateGetDoctor($data);

        if (!empty($errors)) {
            $errors = json_encode($errors);
            $response->getBody()->write($errors);
            return $response;
        }

        $specialization = $data['specialization'];

        if (empty($specialization)) {
            $doctors = $this->doctorRepository->findAll();
        } else {
            $doctors = $this->doctorRepository->findBy(['specialization' => $specialization]);
        }

        $arrDoctors = [];

        foreach ($doctors as $doctor) {
            $doctor = $doctor->toArray();
            $arrDoctors[] = $doctor; //add element
        }
        $response->getBody()->write(json_encode($arrDoctors));

        return $response;
    }

    private function validateGetDoctor(array $data):array
    {
        $errors = [];

        if (mb_strlen($data['specialization']) > 50) {
            $errors["specialization"] = "Специальность не должна быть больше 50 символов";
        }
        return $errors;
    }
}