<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductionCenterDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllPCenterByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pc.id_production_center, pc.id_company, pc.production_center, IFNULL(IF((SELECT id_expense FROM expenses WHERE id_production_center = pc.production_center LIMIT 1) = 0, 
                                             (SELECT id_expenses_distribution FROM expenses_distribution WHERE id_production_center = pc.production_center LIMIT 1), (SELECT id_expense FROM expenses WHERE id_production_center = pc.production_center LIMIT 1)), 0) AS status 
                                      FROM productions_center pc 
                                      WHERE pc.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productions_center = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("productions_center", array('productions_center' => $productions_center));
        return $productions_center;
    }

    public function insertPCenterByCompany($dataPCenter, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO productions_center (id_company, production_center) VALUES (:id_company ,:production_center)");
            $stmt->execute([
                'id_company'  => $id_company,
                'production_center' => strtoupper(trim($dataPCenter['production']))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Produccion duplicada. Ingrese una nueva';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updatePCenter($dataPCenter)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE productions_center SET production_center = :production_center WHERE id_production_center = :id_production_center");
            $stmt->execute([
                'production_center' => strtoupper(trim($dataPCenter['production'])),
                'id_production_center' => $dataPCenter['idProductionCenter'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deletePCenter($id_production_center)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM productions_center WHERE id_production_center = :id_production_center");
            $stmt->execute(['id_production_center' => $id_production_center]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM productions_center WHERE id_production_center = :id_production_center");
                $stmt->execute(['id_production_center' => $id_production_center]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Produccion asociada a un gasto. Imposible Eliminar';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
