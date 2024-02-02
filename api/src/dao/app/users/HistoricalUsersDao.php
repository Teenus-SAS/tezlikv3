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
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("users", array('users' => $users));
        return $users;
    }

    public function insertHistoricalUser($id_user)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $date = date('Y-m-d');

            $stmt = $connection->prepare("INSERT INTO historical_users (id_user, date) VALUES(:id_user, :date)");
            $stmt->execute([
                'id_user' => $id_user,
                'date' => $date
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
