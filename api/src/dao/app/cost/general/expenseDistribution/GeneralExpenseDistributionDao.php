<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralExpenseDistributionDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Consultar data expenses_distribution */
    public function findExpenseDistributionByIdProduct($dataExpensesDistribution)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM expenses_distribution WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => trim($dataExpensesDistribution['idOldProduct'])
        ]);
        $findExpenseDistribution = $stmt->fetch($connection::FETCH_ASSOC);
        return $findExpenseDistribution;
    }

    public function deleteExpensesDistributionByProduct($dataExpensesDistribution)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM expenses_distribution WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $dataExpensesDistribution['idProduct']]);
        $row = $stmt->rowCount();

        if ($row > 0) {
            $stmt = $connection->prepare("DELETE FROM expenses_distribution WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $dataExpensesDistribution['idProduct']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
