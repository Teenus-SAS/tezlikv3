<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class TotalExpenseDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findTotalExpenseByCompany($id_company)
    {

        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT total_expense FROM general_data
                                      WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $totalExpense = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("expenses", array('expenses' => $totalExpense));
        return $totalExpense;
    }

    public function calcTotalExpenseByCompany($id_company)
    {

        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT IFNULL(SUM(expense_value), 0) AS expenses_value 
                                      FROM expenses WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $totalExpense = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("expenses", array('expenses' => $totalExpense));
        return $totalExpense;
    }

    public function calcTotalCPExpenseByCompany($id_company)
    {

        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT IFNULL(SUM(e.expense_value), 0) + IFNULL(SUM(ecp.expense_value), 0) AS expenses_value 
                                      FROM expenses e
                                        LEFT JOIN expenses_products_centers ecp ON ecp.id_expense = e.id_expense
                                      WHERE e.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $totalExpense = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("expenses", array('expenses' => $totalExpense));
        return $totalExpense;
    }

    public function insertTotalExpense($data, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO general_data (total_expense, id_company)
                                      VALUES (:total_expense, :id_company)");
            $stmt->execute([
                'total_expense' => $data['expenses_value'],
                'id_company' => $id_company
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updateTotalExpense($data, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE general_data SET total_expense = :total_expense 
                                    WHERE id_company = :id_company");
            $stmt->execute([
                'total_expense' => $data['expenses_value'],
                'id_company' => $id_company
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
