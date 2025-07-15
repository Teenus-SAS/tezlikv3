<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class HistoricalUsersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllUsers()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM historical_user");
        $stmt->execute();

        $users = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $users;
    }

    public function insertHistoricalUser($id_user)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $sql = "INSERT INTO historical_users (id_user) VALUES(:id_user)";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_user' => $id_user]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
