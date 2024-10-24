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

        $stmt = $connection->prepare("SELECT (SELECT CONCAT(cp.number_count, ' - ', cp.count) FROM puc cp WHERE cp.number_count = (SUBSTRING(p.number_count, 1, 2))) AS puc, e.id_expense, IFNULL(ecp.id_expense_product_center, 0) AS id_expense_product_center, 
                                              e.id_puc, p.number_count, p.count, IFNULL(ecp.id_production_center, 0) AS id_production_center, IFNULL(pc.production_center, '') AS production_center, IFNULL(ecp.expense_value, 0) AS expense_value, IFNULL(ecp.participation, 0) AS participation
                                      FROM expenses e
                                        LEFT JOIN expenses_products_centers ecp ON ecp.id_expense = e.id_expense
                                        LEFT JOIN productions_center pc ON pc.id_production_center = ecp.id_production_center
                                        INNER JOIN puc p ON e.id_puc = p.id_puc
                                      WHERE e.id_company = :id_company AND ecp.participation > 0
                                    ORDER BY CAST(SUBSTRING(p.number_count, 1, 2) AS UNSIGNED), CAST(SUBSTRING(p.number_count, 1, 4) AS UNSIGNED), CAST(SUBSTRING(p.number_count, 1, 5) AS UNSIGNED)");
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
            'id_production_center' => trim($dataExpense['production'])
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
                'expense_value' => trim($dataExpense['expenseValue1'])
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
                                      WHERE id_expense_product_center = :id_expense_product_center");
            $stmt->execute([
                'expense_value' => trim($dataExpense['expenseValue1']),
                'id_production_center' => $dataExpense['production'],
                'id_expense_product_center' => trim($dataExpense['idExpenseProductionCenter'])
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteExpenses($id_expense_product_center)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM expenses_products_centers WHERE id_expense_product_center = :id_expense_product_center");
        $stmt->execute(['id_expense_product_center' => $id_expense_product_center]);
        $row = $stmt->rowCount();

        if ($row > 0) {
            $stmt = $connection->prepare("DELETE FROM expenses_products_centers WHERE id_expense_product_center = :id_expense_product_center");
            $stmt->execute(['id_expense_product_center' => $id_expense_product_center]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
