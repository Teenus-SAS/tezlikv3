<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProcessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProcessByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT p.id_process, p.process, p.id_company, IFNULL((SELECT COUNT(id_payroll) 
                FROM payroll WHERE id_process = p.id_process), 0) AS count_payroll, p.route
                FROM process p 
                WHERE p.id_company = :id_company ORDER BY p.route ASC";

        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);



        $processes = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $processes;
    }

    public function insertProcessByCompany($dataProcess, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO process (id_company ,process) VALUES (:id_company ,:process)");
            $stmt->execute([
                'id_company'  => $id_company,
                'process' => strtoupper(trim($dataProcess['process']))
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Proceso duplicado. Ingrese una nuevo proceso';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateProcess($dataProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE process SET process = :process WHERE id_process = :id_process");
            $stmt->execute([
                'process' => strtoupper(trim($dataProcess['process'])),
                'id_process' => $dataProcess['idProcess'],
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteProcess($id_process)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM process WHERE id_process = :id_process");
            $stmt->execute(['id_process' => $id_process]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM process WHERE id_process = :id_process");
                $stmt->execute(['id_process' => $id_process]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Proceso asociado a un producto/nomina. Imposible Eliminar';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
