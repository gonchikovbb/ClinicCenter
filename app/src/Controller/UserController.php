<?php
namespace App\Controller;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Ramsey\Uuid\Uuid;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

//class UserController
//{
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
//    }
//}

final class UserController
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    //Регистрация
    public function signUp(Request $request, Response $response, $args)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $errors = $this->validateSignUp($data);
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

        $role = $this->roleRepository->findOneBy(['name' => 'client']);

        if (!$role instanceof Role) {
            $response->withStatus(500);
            $response->getBody()->write("Error server");

            return $response;
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        $user = new User(
            $firstName,
            $secondName,
            $gender,
            $email,
            $password,
            $phone,
            $role,
            $thirdName);

        $this->userRepository->save($user, true);

        $user = $user->toArray();

        $response->getBody()->write(json_encode($user));

        return $response;
    }

    private function validateSignUp(array $data):array
    {
        $errors = [];
        if (empty($data['first_name'])) {
            $errors["first_name"] = "Имя не должно быть пустым";
        } elseif (mb_strlen($data['first_name']) > 30) {
            $errors["first_name"] = "Имя не должно быть больше 30 символов";
        }

        if (empty($data['second_name'])) {
            $errors["second_name"] = "Фамилие не должно быть пустым";
        } elseif (mb_strlen($data['second_name']) > 30) {
            $errors["second_name"] = "Фамилие не должно быть больше 30 символов";
        }

        if (!in_array($data["gender"], ["male","female"])) {
            $errors["gender"] = "Введите пол 'male' или 'female";
        }

        $errorsEmail = $this->validateEmail($data);
        if (!empty($errorsEmail)) {
            $errors["email"] = $errorsEmail;
        }

        $errorsPass = $this->validatePass($data);
        if (!empty($errorsPass)) {
            $errors["password"] = $errorsPass;
        }

        if (empty($data['phone'])) {
            $errors["phone"] = "Телефон не должен быть пустым";
        } elseif (mb_strlen($data['phone']) > 11) {
            $errors["phone"] = "Телефон не должен быть больше 11 символов";
        }

        if (!empty($data['third_name']) && (mb_strlen($data['third_name']) > 30)) {
            $errors["third_name"] = "Отчество не должно быть больше 30 символов";
        }
        return $errors;
    }
    //Авторизация
    public function signIn(Request $request, Response $response, $args)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $errors = $this->validateSignIn($data);
        if (!empty($errors)) {
            $response->getBody()->write(json_encode($errors));
            return $response;
        }

        $email = $data['email'];
        $password = $data['password'];

        $user = $this->userRepository->findOneBy(['email'=>$email]);

        if (!$user instanceof User) {
            $errors["email"] = "Пользователь с такой почтой не зарегистрирован";
        } elseif (!password_verify($password, $user->getPassword())) {
            $errors["password"] = "Пароль введен не верно";
        }

        if (!empty($errors)) {
            $response->getBody()->write(json_encode($errors));
            return $response;
        }
        $token = Uuid::uuid1()->toString(); //статический метод, сгенерирует уникальное значение, нужно ее сохранить в юзерс и выдать в респонс
        //token  headers <authorization>
        $user->setToken($token);
        $response = $response->withHeader("Authorization", $token);
        $this->userRepository->save($user, true);

        $user = $user->toArray();
        $response->getBody()->write(json_encode($user));
        return $response;
    }
    private function validateSignIn(array $data):array
    {
        $errors = [];

        $errorsEmail = $this->validateEmail($data);
        if (!empty($errorsEmail)) {
            $errors["email"] = $errorsEmail;
        }

        $errorsPass = $this->validatePass($data);
        if (!empty($errorsPass)) {
            $errors["password"] = $errorsPass;
        }
        return $errors;
    }
    private function validateEmail(array $data):?string
    {
        if (empty($data['email'])) {
            return "Почта не должна быть пустой";
        }
        elseif (mb_strlen($data['email']) > 30) {
            return "Почта не должна быть больше 30 символов";
        }
        return null;
    }
    private function validatePass(array $data):?string
    {
        if (empty($data['password'])) {
            return  "Пароль не должен быть пустым";
        }
        elseif (mb_strlen($data['password']) > 30) {
            return "Пароль не должен быть больше 30 символов";
        }
        return null;
    }

    public function editUser(Request $request, Response $response, $args)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $errors = $this->validateEditUser($data);
        if (!empty($errors)) {
            $errors = json_encode($errors);
            $response->getBody()->write($errors);
            return $response;
        }

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

        if (empty($data)) {
            $user = $user->toArray();
            $response->getBody()->write(json_encode($user));
            return $response;
        }

        if (!empty($data['first_name'])) {
            if ($data['first_name'] !== $user->getFirstName()) {
                $firstName = $data['first_name'];
                $user->setFirstName($firstName);
            }
        }

        if (!empty($data['second_name'])) {
            if ($data['second_name'] !== $user->getSecondName()) {
                $secondName = $data['second_name'];
                $user->setSecondName($secondName);
            }
        }

        if (!empty($data['gender'])) {
            if ($data['gender'] !== $user->getGender()) {
                $gender = $data['gender'];
                $user->setGender($gender);
            }
        }

        if (!empty($data['email'])) {
            if ($data['email'] !== $user->getEmail()) {
                $email = $data['email'];
                $user->setEmail($email);
            }
        }

        if (!empty($data['password'])) {
            if ($data['password'] !== $user->getPassword()) {
                $password = $data['password'];
                $password = password_hash($password, PASSWORD_DEFAULT);
                $user->setPassword($password);
            }
        }

        if (!empty($data['phone'])) {
            if ($data['phone'] !== $user->getPhone()) {
                $phone = $data['phone'];
                $user->setPhone($phone);
            }
        }

        if (!empty($data['third_name'])) {
            if ($data['third_name'] !== $user->getThirdName()) {
                $thirdName = $data['third_name'];
                $user->setThirdName($thirdName);
            }
        }

        $this->userRepository->save($user, true);

        $user = $user->toArray();

        $response->getBody()->write(json_encode($user));

        return $response;
    }

    private function validateEditUser(array $data):array
    {
        $errors = [];

        if (!empty($data['first_name']) && (mb_strlen($data['first_name']) > 30)) {
            $errors["first_name"] = "Имя не должно быть больше 30 символов";
        }

        if (!empty($data['second_name']) && (mb_strlen($data['second_name']) > 30)) {
            $errors["second_name"] = "Фамилие не должно быть больше 30 символов";
        }

        if (!empty($data['gender']) && !in_array($data["gender"], ["male","female"])) {
            $errors["gender"] = "Введите пол 'male' или 'female";
        }

        if (!empty($data['email']) && (mb_strlen($data['email']) > 30)) {
            $errors["email"] = "Фамилие не должно быть больше 30 символов";
        }

        if (!empty($data['password']) && (mb_strlen($data['password']) > 30)) {
            $errors["password"] = "Фамилие не должно быть больше 30 символов";
        }

        if (!empty($data['phone']) && (mb_strlen($data['phone']) > 11)) {
            $errors["phone"] = "Телефон не должен быть больше 11 символов";
        }

        if (!empty($data['third_name']) && (mb_strlen($data['third_name']) > 30)) {
            $errors["third_name"] = "Отчество не должно быть больше 30 символов";
        }
        return $errors;
    }

}
