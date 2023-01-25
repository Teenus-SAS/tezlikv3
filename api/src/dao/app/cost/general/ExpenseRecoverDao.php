<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ExpenseRecoverDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllExpenseRecoverByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT er.id_expense_recover, p.id_product, p.reference, p.product, er.expense_recover
                                      FROM products p
                                        INNER JOIN expenses_recover er ON p.id_product = er.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $recoverExpense = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("recoverExpense", array('recoverExpense' => $recoverExpense));
        return $recoverExpense;
    }

    public function findExpenseRecover($dataExpense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_expense_recover FROM expenses_recover 
                                      WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => trim($dataExpense['idProduct']),
            'id_company' => $id_company
        ]);
        $expenseRecover = $stmt->fetch($connection::FETCH_ASSOC);
        return $expenseRecover;
    }

    public function findAllProducts($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products p WHERE p.id_company = :id_company
                                      AND p.id_product NOT IN (SELECT id_product FROM expenses_recover WHERE id_product = p.id_product)");
        $stmt->execute(['id_company' => $id_company]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function insertRecoverExpenseByCompany($dataExpense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO expenses_recover (id_product, id_company, expense_recover) VALUES (:id_product, :id_company, :expense_recover)");
            $stmt->execute([
                'id_product' => $dataExpense['idProduct'],
                'id_company' => $id_company,
                'expense_recover' => $dataExpense['percentage']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            if ($e->getCode() == 23000)
                $message = 'Producto ya registrado. Intente con uno nuevo';
            else $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateRecoverExpense($dataExpense)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE expenses_recover SET expense_recover = :expense_recover WHERE id_expense_recover = :id_expense_recover");
            $stmt->execute([
                'expense_recover' => $dataExpense['percentage'],
                'id_expense_recover' => $dataExpense['idExpenseRecover']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteRecoverExpense($id_expense_recover)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM expenses_recover WHERE id_expense_recover = :id_expense_recover");
            $stmt->execute(['id_expense_recover' => $id_expense_recover]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM expenses_recover WHERE id_expense_recover = :id_expense_recover");
                $stmt->execute(['id_expense_recover' => $id_expense_recover]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteRecoverExpenseByProduct($dataExpense)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM expenses_recover WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $dataExpense['idProduct']]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM expenses_recover WHERE id_product = :id_product");
                $stmt->execute(['id_product' => $dataExpense['idProduct']]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
