<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCostUserAccessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findPrincipalUserByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM cost_users_access cua
                                          INNER JOIN users u ON u.id_user = cua.id_user
                                      WHERE u.id_company = :id_company AND cua.contract = 1");
        $stmt->execute(['id_company' => $id_company]);
        $user = $stmt->fecth($connection::FETCH_ASSOC);
        return $user;
    }

    public function changePrincipalUser($dataUser)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE cost_users_access cua
                                          INNER JOIN users u ON u.id_user = cua.id_user
                                          SET contract = 0 WHERE u.id_company = :id_company");
            $stmt->execute([
                'id_company' => $dataUser['company']
            ]);
            $stmt = $connection->prepare("UPDATE cost_users_access SET contract = :contract WHERE id_user = :id_user");
            $stmt->execute([
                'contract' => 1,
                'id_user' => $dataUser['id_user']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
