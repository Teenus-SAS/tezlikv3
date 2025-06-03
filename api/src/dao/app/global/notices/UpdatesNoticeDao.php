<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UpdatesNoticeDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function updatesNoticeDao($id_user)
  {
    $connection = Connection::getInstance()->getConnection();
    $sql = "UPDATE users SET updates_notice = 1 WHERE id_user = :id_user";
    $stmt = $connection->prepare($sql);
    $stmt->execute(['id_user' => $id_user]);
  }
}
