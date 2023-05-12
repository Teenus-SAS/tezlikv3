<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralExpenseRecoverDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsNotInERecover($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products p WHERE p.id_company = :id_company
                                      AND p.id_product NOT IN (SELECT id_product FROM expenses_recover WHERE id_product = p.id_product)");
        $stmt->execute(['id_company' => $id_company]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findExpenseRecoverByIdProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.reference, p.product, er.id_expense_recover, er.id_product, er.id_company, er.expense_recover
                                      FROM expenses_recover er
                                        INNER JOIN products p ON p.id_product = er.id_product
                                      WHERE p.id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product,
        ]);
        $expenseRecover = $stmt->fetch($connection::FETCH_ASSOC);
        return $expenseRecover;
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
