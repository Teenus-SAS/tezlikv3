<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class StatusActiveUserDao
{
  private $logger;

  public function __construct()
  {
    //$this->logger = new Logger(self::class);
    //$this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findSessionUser($id_user)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT session_active FROM users WHERE id_user = :id_user");
    $stmt->execute(['id_user' => $id_user]);
    $session = $stmt->fetch($connection::FETCH_ASSOC);
    return $session;
  }

  /* Actualizar estado de sesion de Usuario */
  public function changeStatusUserLogin()
  {
    $id_user = $_SESSION['idUser'];
    $case = $_SESSION['case'];

    $connection = Connection::getInstance()->getConnection();

    if ($case == 1) {
      $session = $this->findSessionUser($id_user);
      $session = $session['session_active'];

      ($session == 1 ? $session = 0 : $session == 0) ? $session = 1 : $session;

      $sql = "UPDATE users SET session_active = :session_active WHERE id_user = :id_user";

      $stmt = $connection->prepare($sql);
      $stmt->execute([
        'session_active' => $session,
        'id_user' => $id_user
      ]);
    } else if ($case == 2) {

      $sql = "SELECT session_active FROM admins WHERE id_admin = :id_admin";

      $stmt = $connection->prepare($sql);
      $stmt->execute(['id_admin' => $id_user]);
      $session = $stmt->fetch($connection::FETCH_ASSOC);

      $session = $session['session_active'];
      ($session == 1 ? $session = 0 : $session == 0) ? $session = 1 : $session;

      $sql = "UPDATE admins SET session_active = :session_active WHERE id_admin = :id_admin";
      $stmt = $connection->prepare($sql);
      $stmt->execute([
        'session_active' => $session,
        'id_admin' => $id_user
      ]);
    }
  }

  public function deactivateSession(int $id_company, int $id_user): bool
  {
    $connection = Connection::getInstance()->getConnection();

    $sql = "UPDATE users SET session_active = 0 
            WHERE id_user = :id_user AND id_company = :id_company";

    try {
      $stmt = $connection->prepare($sql);
      $stmt->execute(['id_user' => $id_user, 'id_company' => $id_company]);
      return $stmt->rowCount() > 0;
    } catch (\PDOException $e) {
      error_log("Error al desactivar sesión: " . $e->getMessage());
      throw $e;
    }
  }
}
