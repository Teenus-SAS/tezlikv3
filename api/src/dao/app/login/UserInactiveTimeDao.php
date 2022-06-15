<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
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
    @session_start();
    if (empty($_SESSION['active']) || time() - $_SESSION['time'] > 600) {
      //$connection = Connection::getInstance()->getConnection();
      //$this->changeStatusUserLogin();

      session_destroy();
      echo "<script> window.location='http://tezlikv2/'; </script>";
      exit();
    } else
      @session_start();
  }
}
