<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LastLoginDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findLastLogin()
    {
        $connection = Connection::getInstance()->getConnection();
        @session_start();
        $id_user = $_SESSION['idUser'];
        $case = $_SESSION['case'];

        try {
            if ($case == 1) {
                $stmt = $connection->prepare("UPDATE users SET last_login = now() WHERE id_user = :id_user");
                $stmt->execute(['id_user' => $id_user]);
            } else if ($case == 2) {
                $stmt = $connection->prepare("UPDATE admins SET last_login = now() WHERE id_admin = :id_admin");
                $stmt->execute(['id_admin' => $id_user]);
            }
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
