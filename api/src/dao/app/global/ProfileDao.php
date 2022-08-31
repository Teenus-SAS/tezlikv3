<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProfileDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function updateProfile($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();

        $pass = password_hash($dataUser['pass'], PASSWORD_DEFAULT);

        try {
            $stmt = $connection->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, username = :username, email = :email, password = :password
                                          WHERE id_user = :id_user");
            $stmt->execute([
                'firstname' => $dataUser['nameUser'],
                'lastname' => $dataUser['lastnameUser'],
                'username' => $dataUser['username'],
                'email' => $dataUser['emailUser'],
                'password' => $pass,
                'id_user' => $dataUser['idUser']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
