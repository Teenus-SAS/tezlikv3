<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FirstLoginDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function saveDataUser($dataUser, $id_user)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, telephone = :telephone 
                                          WHERE id_user = :id_user");
            $stmt->execute([
                'firstname' => $dataUser['firstname'],
                'lastname' => $dataUser['lastname'],
                'telephone' => $dataUser['telephone'],
                'id_user' => $id_user,
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
