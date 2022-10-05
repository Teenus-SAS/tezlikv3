<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UserAdminDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllUserAdmins()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM admins");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $admins = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("usuarios Obtenidos", array('usuarios' => $admins));
        return $admins;
    }

    public function findUserAdmin($email)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM admins WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $admin = $stmt->fetch($connection::FETCH_ASSOC);
        return $admin;
    }

    public function insertUserAdmin($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $pass = hash("sha256", $dataUser['password']);

            $stmt = $connection->prepare("INSERT INTO admins (email, password) 
                                          VALUES (:email, :pass)");
            $stmt->execute([
                'email' => $dataUser['email'],
                'pass' => $pass
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Email duplicado. Ingrese una nuevo email';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateUser($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $pass = hash("sha256", $dataUser['password']);

            $stmt = $connection->prepare("UPDATE admins SET email = :email, password = :pass
                                      WHERE id_admin = :id_admin");
            $stmt->execute([
                'id_admin' => $dataUser['idAdmin'],
                'email' => $dataUser['email'],
                'pass' => $pass
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Email duplicado. Ingrese una nuevo email';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteUser($id_admin)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("DELETE FROM admins WHERE id_admin = :id_admin");
            $stmt->execute(['id_admin' => $id_admin]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
