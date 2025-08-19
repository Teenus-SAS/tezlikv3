<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DataHistoricalDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function getHistoricalExpenses(int $id_company, $connection = null)
    {
        $useExternalConnection = $connection !== null;
        $stmt = null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {

            $sql = "SELECT *
                    FROM tezlikso_HistProduccion.historical_expenses
                    WHERE id_company = :id_company;";

            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_company' => $id_company]);

            $expenses = $stmt->fetchAll($connection::FETCH_ASSOC);
            return $expenses;
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__, ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            if ($stmt) $stmt->closeCursor();
            if (!$useExternalConnection && $connection) $connection = null;
        }
    }

    public function getHistoricalDistribution(int $id_company, $connection = null)
    {
        $useExternalConnection = $connection !== null;
        $stmt = null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {

            $sql = "SELECT * FROM tezlikso_HistProduccion.historical_expense_distribution 
                    WHERE id_company = :id_company;";

            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_company' => $id_company]);

            $distribution = $stmt->fetchAll($connection::FETCH_ASSOC);
            return $distribution;
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__, ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            if ($stmt) $stmt->closeCursor();
            if (!$useExternalConnection && $connection) $connection = null;
        }
    }

    public function getHistoricalProducts(int $id_company, $connection = null)
    {
        $useExternalConnection = $connection !== null;
        $stmt = null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {

            $sql = "SELECT hp.*, p.product FROM tezlikso_HistProduccion.historical_products hp
                    JOIN tezlikso_tezlikProduccion.products p ON p.id_product = hp.id_product 
                    WHERE hp.id_company = :id_company;";

            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_company' => $id_company]);

            $products = $stmt->fetchAll($connection::FETCH_ASSOC);
            return $products;
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__, ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            if ($stmt) $stmt->closeCursor();
            if (!$useExternalConnection && $connection) $connection = null;
        }
    }
}
