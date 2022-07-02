<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class InvMoldsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllInvMold($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM inv_molds WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $molds = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("Moldes", array('Moldes' => $molds));
        return $molds;
    }

    public function findInvMold($dataMold, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM inv_molds 
                                      WHERE reference = :reference AND mold = :mold AND id_company = :id_company");
        $stmt->execute([
            'reference' => $dataMold['referenceMold'],
            'mold' => ucfirst(strtolower(trim($dataMold['mold']))),
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $molds = $stmt->fetch($connection::FETCH_ASSOC);
        return $molds;
    }

    public function insertInvMoldByCompany($dataMold, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO inv_molds (reference, mold, id_company, assembly_time)
                                          VALUES (:reference, :mold, :id_company, :assembly_time)");
            $stmt->execute([
                'reference' => $dataMold['referenceMold'],
                'mold' => ucfirst(strtolower(trim($dataMold['mold']))),
                'id_company' => $id_company,
                'assembly_time' => $dataMold['assemblyTime']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Molde duplicado. Ingrese una nuevo molde';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateInvMold($dataMold)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE inv_molds SET reference = :reference, mold = :mold, assembly_time = :assembly_time
                                          WHERE id_mold = :id_mold");
            $stmt->execute([
                'reference' => $dataMold['referenceMold'],
                'mold' => ucfirst(strtolower(trim($dataMold['mold']))),
                'assembly_time' => $dataMold['assemblyTime'],
                'id_mold' => $dataMold['idMold'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteInvMold($id_mold)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM inv_molds WHERE id_mold = :id_mold");
            $stmt->execute(['id_mold' => $id_mold]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM inv_molds WHERE id_mold = :id_mold");
                $stmt->execute(['id_mold' => $id_mold]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Molde asociado a un producto. Imposible Eliminar';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
