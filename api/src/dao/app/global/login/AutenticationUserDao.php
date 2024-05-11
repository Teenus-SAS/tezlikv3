<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AutenticationUserDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findByEmail($email)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch($connection::FETCH_ASSOC);

    if (!$user) {
      $stmt = $connection->prepare("SELECT * FROM admins WHERE email = :email");
      $stmt->execute(['email' => $email]);
      $user = $stmt->fetch($connection::FETCH_ASSOC);
      if ($user)
        $user['rol'] = 'admin';
    }

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    $this->logger->notice("usuarios Obtenidos", array('usuarios' => $user));
    return $user;
  }

  public function getToken()
  {
    // $headers = apache_request_headers();
    if (!isset($_SESSION))
      session_start();
    if (!isset($_SESSION['token'])) {
      return 1;
    }

    // $authorization = $headers['Authorization'];
    // $authorizationArray = explode(' ', $authorization);

    // $token = $authorizationArray['1'];
    $token = $_SESSION['token'];
    try {
      $decoded = JWT::decode($token, new Key($_ENV['jwt_key'], 'HS256'));
    } catch (\Exception $e) {
      return ['info' => $e->getMessage()];
    }
    return $decoded;
  }

  public function validationToken($info)
  {
    try {
      $connection = Connection::getInstance()->getConnection();
      $stmt = $connection->prepare("SELECT * FROM users WHERE id_user = :id_user");
      $stmt->execute(['id_user' => $info->data]);
      $user = $stmt->fetch($connection::FETCH_ASSOC);
      return $user;
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  /* public function checkUserAdmin($dataUser)
  {
    $stmt = $connection->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->execute(['email' => $dataUser]);
    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    $user = $stmt->fetch($connection::FETCH_ASSOC);

    return $user;
  } */
}
