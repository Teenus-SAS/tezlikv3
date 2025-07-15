<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UsersDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAll()
  {
    session_start();
    $id_company = $_SESSION['id_company'];

    $connection = Connection::getInstance()->getConnection();

    if ($id_company == 1)
      $stmt = $connection->prepare("SELECT * FROM users WHERE id_company = 2  ORDER BY firstname");
    else if ($id_company == 4)
      $stmt = $connection->prepare("SELECT * FROM users ORDER BY firstname");

    $stmt->execute();

    $users = $stmt->fetchAll($connection::FETCH_ASSOC);

    return $users;
  }

  public function findAllUsersByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    if ($id_company == 0) {
      $stmt = $connection->prepare("SELECT * FROM users");
      $stmt->execute();
    } else {
      $stmt = $connection->prepare("SELECT * FROM users WHERE id_company = :id_company;");
      $stmt->execute(['id_company' => $id_company]);
    }



    $users = $stmt->fetchAll($connection::FETCH_ASSOC);

    return $users;
  }

  public function findUser($email)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM users u WHERE email = :email");
    $stmt->execute(['email' => trim($email)]);
    $user = $stmt->fetch($connection::FETCH_ASSOC);



    return $user;
  }

  public function saveUser($dataUser, $pass, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    try {
      $stmt = $connection->prepare("INSERT INTO users (firstname, lastname, email, password, id_company, id_rols, active) 
                                    VALUES(:firstname, :lastname, :email, :pass, :id_company, :id_rols, :active)");
      $stmt->execute([
        'firstname' => ucwords(strtolower(trim($dataUser['nameUser']))),
        'lastname' => ucwords(strtolower(trim($dataUser['lastnameUser']))),
        'email' => strtolower(trim($dataUser['emailUser'])),
        'pass' => $pass,
        'id_company' => $id_company,
        'id_rols' => 2,
        'active' => 1
      ]);
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  // Insertar nuevo usuario solo con email 
  public function saveUserDemo($dataUser, $pass, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    try {
      $stmt = $connection->prepare("INSERT INTO users (firstname, lastname, telephone, email, password, id_company, id_rols, active) 
                                    VALUES(:firstname, :lastname, :telephone, :email, :pass, :id_company, :id_rols, :active)");
      $stmt->execute([
        'firstname' => ucwords(strtolower(trim($dataUser['nameUser']))),
        'lastname' => ucwords(strtolower(trim($dataUser['lastnameUser']))),
        'telephone' => trim($dataUser['phone']),
        'email' => strtolower(trim($dataUser['emailUser'])),
        'pass' => $pass,
        'id_company' => $id_company,
        'id_rols' => 2,
        'active' => 1
      ]);
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function updateUser($dataUser, $pathAvatar)
  {
    $connection = Connection::getInstance()->getConnection();

    if ($pathAvatar == null) {
      $stmt = $connection->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, active = :active
                                      WHERE id_user = :id_user");
      $stmt->execute([
        'firstname' => ucwords(strtolower(trim($dataUser['nameUser']))),
        'lastname' => ucwords(strtolower(trim($dataUser['lastnameUser']))),
        'active' => 1,
        'id_user' => $dataUser['id_user']
      ]);
    } else {

      $stmt = $connection->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, avatar = :avatar, active = :active
                                        WHERE id_user = :id_user");
      $stmt->execute([
        'firstname' => ucwords(strtolower(trim($dataUser['nameUser']))),
        'lastname' => ucwords(strtolower(trim($dataUser['lastnameUser']))),
        'avatar' => $pathAvatar,
        'active' => 1,
        'id_user' => $dataUser['id_user']
      ]);
    }
  }


  public function deleteUser($dataUser)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("DELETE FROM users WHERE id_user = :id");
      $stmt->execute(['id' => $dataUser['id_user']]);
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
