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

        try {
            if (empty($dataUser['password'])) {
                $stmt = $connection->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, position = :position
                                          WHERE id_user = :id_user");
                $stmt->execute([
                    'id_user' => $dataUser['idUser'],
                    'firstname' => $dataUser['nameUser'],
                    'lastname' => $dataUser['lastnameUser'],
                    'position' => $dataUser['position'],
                ]);
            } else {
                $pass = password_hash($dataUser['password'], PASSWORD_DEFAULT);
                $stmt = $connection->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, position = :position, password = :pass
                                          WHERE id_user = :id_user");
                $stmt->execute([
                    'id_user' => $dataUser['idUser'],
                    'firstname' => $dataUser['nameUser'],
                    'lastname' => $dataUser['lastnameUser'],
                    'position' => $dataUser['position'],
                    'pass' => $pass
                ]);
            }
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateProfileAdmin($dataAdmin)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if (empty($dataAdmin['password'])) {
                $stmt = $connection->prepare("UPDATE admins SET firstname = :firstname, lastname = :lastname
                                          WHERE id_admin = :id_admin");
                $stmt->execute([
                    'id_admin' => $dataAdmin['idUser'],
                    'firstname' => $dataAdmin['nameUser'],
                    'lastname' => $dataAdmin['lastnameUser']
                ]);
            } else {
                $pass = password_hash($dataAdmin['password'], PASSWORD_DEFAULT);
                $stmt = $connection->prepare("UPDATE admins SET firstname = :firstname, lastname = :lastname, password = :pass
                                          WHERE id_admin = :id_admin");
                $stmt->execute([
                    'id_admin' => $dataAdmin['idUser'],
                    'firstname' => $dataAdmin['nameUser'],
                    'lastname' => $dataAdmin['lastnameUser'],
                    'pass' => $pass
                ]);
            }
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
