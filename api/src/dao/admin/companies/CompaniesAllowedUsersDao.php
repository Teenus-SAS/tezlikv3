<?php

namespace tezlikv3\dao;

use DateTime;
use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CompaniesAllowedUsersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    //Obtener cantidad de usuarios permitidos por empresas activas
    public function usersAllowed()
    {
        $stmt = null;
        $connection = Connection::getInstance()->getConnection();
        try {
            $sql = "SELECT cp.company, cl.quantity_user FROM companies cp 
                    INNER JOIN companies_licenses cl ON cp.id_company = cl.id_company
                    WHERE cl.license_status = 0";
            $stmt = $connection->prepare($sql);
            $stmt->execute();

            $allowedData = $stmt->fetchAll($connection::FETCH_ASSOC);
            return $allowedData;
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__, ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            if ($stmt)
                $stmt->closeCursor();
        }
    }


    //ACTUALIZAR CANTIDAD DE USUARIOS PERMITIDOS POR EMPRESA
    public function updateUsersAllowed($quantity_user, $id_company_license)
    {
        $stmt = null;
        $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "UPDATE companies_licenses SET quantity_user = :quantity_user
                    WHERE id_company_license = :id_company_license";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_company_license' => $id_company_license,
                'quantity_user' => $quantity_user
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__, ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            if ($stmt)
                $stmt->closeCursor();
        }
    }
}
