<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

require_once __DIR__ . '/StatusActiveUserDao.php';

class UserInactiveTimeDao extends StatusActiveUserDao
{
  /* private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  } */

  public function findSession()
  {
    // @session_start();
    // $start_time = $_SESSION['time'];

    // $end_time = microtime(true);

    // $duration = $end_time - $start_time;
    // $hours = (int)($duration / 60 / 60);

    // $minutes = (int)($duration / 60) - $hours * 60;

    // if (empty($_SESSION['active']) || $minutes >= 7) {
    //   //$connection = Connection::getInstance()->getConnection();
    //   // $this->changeStatusUserLogin();

    //   session_destroy();
    //   // echo "<script> window.location='/'; </script>";
    //   return 1;
    //   exit();
    // }
    $id_user = $_SESSION['idUser'];
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT session_active FROM users WHERE id_user = :id_user");
    $stmt->execute(['id_user' => $id_user]);
    $session = $stmt->fetch($connection::FETCH_ASSOC);
    return $session;
  }
}
