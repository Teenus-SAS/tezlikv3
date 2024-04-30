<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ExpensesProductionCenterDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllExpensesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT (SELECT CONCAT(cp.number_count, ' - ', cp.count) FROM puc cp WHERE cp.number_count = (SUBSTRING(p.number_count, 1, 2))) AS puc,
                                             e.id_expense, e.id_puc, p.number_count, p.count, e.participation, e.expense_value
                                      FROM expenses e 
                                       INNER JOIN puc p ON e.id_puc = p.id_puc
                                      WHERE e.id_company = :id_company
                                      UNION
                                      SELECT (SELECT CONCAT(cp.number_count, ' - ', cp.count) FROM puc cp WHERE cp.number_count = (SUBSTRING(p.number_count, 1, 2))) AS puc,
                                              ecp.id_production_center AS id_expense, e.id_puc, p.number_count, p.count, ecp.participation, ecp.expense_value
                                      FROM expenses_products_centers ecp
                                        INNER JOIN expenses e ON e.id_expense = ecp.id_expense
                                        INNER JOIN puc p ON e.id_puc = p.id_puc
                                      WHERE ecp.id_company = :id_company  
                                    ORDER BY `puc` ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $expenses = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("expenses", array('expenses' => $expenses));
        return $expenses;
    }

    // Consultar si existe el gasto en BD
    public function findExpense($dataExpense)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM expenses_products_centers 
                                      WHERE id_expense = :id_expense AND id_production_center = :id_production_center");
        $stmt->execute([
            'id_expense' => trim($dataExpense['idExpense']),
            'id_production_center' => trim($dataExpense['idProductionCenter'])
        ]);
        $findExpense = $stmt->fetch($connection::FETCH_ASSOC);
        return $findExpense;
    }

    public function insertExpensesByCompany($dataExpense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO expenses_products_centers (id_company, id_expense, id_production_center, expense_value)
                                    VALUES (:id_company, :id_expense, :id_production_center, :expense_value)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_expense' => trim($dataExpense['idExpense']),
                'id_production_center' => $dataExpense['production'],
                'expense_value' => trim($dataExpense['expenseValue'])
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateExpenses($dataExpense)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE expenses_products_centers SET id_production_center = :id_production_center, expense_value = :expense_value
                                      WHERE id_production_center = :id_production_center");
            $stmt->execute([
                'expense_value' => trim($dataExpense['expenseValue']),
                'id_production_center' => $dataExpense['production'],
                'id_production_center' => trim($dataExpense['idProductionCenter'])
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteExpenses($id_production_center)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM expenses_products_centers WHERE id_production_center = :id_production_center");
        $stmt->execute(['id_production_center' => $id_production_center]);
        $row = $stmt->rowCount();

        if ($row > 0) {
            $stmt = $connection->prepare("DELETE FROM expenses_products_centers WHERE id_production_center = :id_production_center");
            $stmt->execute(['id_production_center' => $id_production_center]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
