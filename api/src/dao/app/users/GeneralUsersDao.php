<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralUsersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function inactivateActivateUser($id_user, $status)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE users SET active = :statusUser WHERE id_user = :id_user");
            $stmt->execute([
                'id_user' => $id_user,
                'statusUser' => $status
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    /*Obtener cantidad para creacion de usuario permitidos*/

    public function quantityUsersAllows($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT quantity_user FROM companies_licenses 
                                  WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $quantity_users_allows = $stmt->fetch($connection::FETCH_ASSOC);



        return $quantity_users_allows;
    }

    /*Obtener cantidad de usuarios creados*/

    public function quantityUsersCreated($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(*) AS quantity_users FROM users WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $quantity_users_created = $stmt->fetch($connection::FETCH_ASSOC);



        return $quantity_users_created;
    }

    /*Obtener cantidad de usuarios activos*/
    /*Obtener cantidad de usuarios permitidos por empresa*/
}
