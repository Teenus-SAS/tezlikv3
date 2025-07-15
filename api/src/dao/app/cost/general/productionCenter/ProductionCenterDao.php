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
        $stmt = $connection->prepare("SELECT pc.id_production_center, pc.id_company,  pc.production_center, COALESCE( CASE WHEN e1.id_expense_product_center IS NULL THEN e2.id_expenses_distribution ELSE e1.id_expense_product_center END, 0) AS status
                                      FROM productions_center pc
                                        LEFT JOIN expenses_products_centers e1 ON pc.id_production_center = e1.id_production_center
                                        LEFT JOIN expenses_distribution e2 ON pc.id_production_center = e2.id_production_center
                                      WHERE pc.id_company = :id_company GROUP BY pc.id_production_center");
        $stmt->execute(['id_company' => $id_company]);



        $productions_center = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $productions_center;
    }

    public function insertPCenterByCompany($dataPCenter, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO productions_center (id_company, production_center) VALUES (:id_company, :production_center)");
            $stmt->execute([
                'production_center' => strtoupper(trim($dataPCenter['production'])),
                'id_company'  => $id_company
            ]);
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
