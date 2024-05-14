<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

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

  /* public function checkUserAdmin($dataUser)
  {
    $stmt = $connection->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->execute(['email' => $dataUser]);
    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    $user = $stmt->fetch($connection::FETCH_ASSOC);

    return $user;
  } */
}
