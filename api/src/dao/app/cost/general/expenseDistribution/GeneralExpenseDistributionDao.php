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

    public function findAllProductsNotInEDistribution($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        // $stmt = $connection->prepare("SELECT * FROM products p WHERE p.id_company = :id_company
        //                               AND p.id_product NOT IN (SELECT id_product FROM expenses_distribution WHERE id_product = p.id_product)
        //                               AND p.active = 1");
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, p.composite, IFNULL(ed.id_expenses_distribution, 0) AS status, pc.new_product,
                                             IFNULL(ed.assignable_expense, 0) AS assignable_expense
                                      FROM products p
                                        LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                        INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1");
        $stmt->execute(['id_company' => $id_company]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    /* Consultar data expenses_distribution */
    public function findExpenseDistributionByIdProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.reference, p.product, ed.id_expenses_distribution, ed.id_product, 
                                             ed.id_company, ed.units_sold, ed.turnover, ed.assignable_expense
                                      FROM expenses_distribution ed
                                        INNER JOIN products p ON p.id_product = ed.id_product
                                      WHERE ed.id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $findExpenseDistribution = $stmt->fetch($connection::FETCH_ASSOC);
        return $findExpenseDistribution;
    }

    public function findAllExpensesDistributionByFamily($id_family)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.reference, p.product, ed.id_expenses_distribution, ed.id_product, 
                                             ed.id_company, ed.units_sold, ed.turnover, ed.assignable_expense
                                      FROM expenses_distribution ed
                                        INNER JOIN products p ON p.id_product = ed.id_product
                                        INNER JOIN families f ON f.id_family = p.id_family
                                      WHERE f.id_family = :id_family");
        $stmt->execute([
            'id_family' => $id_family
        ]);
        $expenseDistributions = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $expenseDistributions;
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
