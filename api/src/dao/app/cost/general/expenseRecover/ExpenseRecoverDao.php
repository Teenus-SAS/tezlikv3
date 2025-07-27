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

        $sql = "SELECT er.id_expense_recover, p.id_product, p.reference, p.product, er.expense_recover, er.manual_recovery
                FROM products p
                INNER JOIN expenses_recover er ON p.id_product = er.id_product
                WHERE p.id_company = :id_company AND p.active = 1";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);

        $recoverExpense = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $recoverExpense;
    }

    public function findExpenseRecover($dataExpense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT id_expense_recover FROM expenses_recover 
                WHERE id_product = :id_product AND id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'id_product' => trim($dataExpense['idProduct']),
            'id_company' => $id_company
        ]);
        $expenseRecover = $stmt->fetch($connection::FETCH_ASSOC);
        return $expenseRecover;
    }

    public function insertRecoverExpenseByCompany($dataExpense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "INSERT INTO expenses_recover (id_product, id_company, expense_recover, manual_recovery) 
                    VALUES (:id_product, :id_company, :expense_recover, :manual_recovery)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_product' => $dataExpense['idProduct'],
                'id_company' => $id_company,
                'expense_recover' => $dataExpense['percentage'],
                'manual_recovery' => 0
            ]);
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
            $sql = "UPDATE expenses_recover SET expense_recover = :expense_recover, manual_recovery = :manual_recovery 
                    WHERE id_expense_recover = :id_expense_recover";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'expense_recover' => $dataExpense['percentage'],
                'id_expense_recover' => $dataExpense['idExpenseRecover'],
                'manual_recovery' => 1
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function changeManualRecovery($id_expense_recover, $id_company, $connection = null)
    {
        $useExternalConnection = $connection !== null;
        $stmt = null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "UPDATE expenses_recover SET manual_recovery = :manual_recovery 
                    WHERE id_expense_recover = :id_expense_recover AND id_company = :id_company";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_expense_recover' => $id_expense_recover,
                'id_company' => $id_company,
                'manual_recovery' => 0
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__, ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            if ($stmt)
                $stmt->closeCursor();

            if (!$useExternalConnection && $connection)
                $connection = null;
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
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
