<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
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

    public function findTotalExpenseByCompany()
    {
        session_start();
        $id_company = $_SESSION['id_company'];

        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT total_expense FROM general_data
                                      WHERE id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $totalExpense = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("expenses", array('expenses' => $totalExpense));
        return $totalExpense;
    }

    public function insertUpdateTotalExpense($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        /* Verificar que exista un registro en la tabla */

        $stmt = $connection->prepare("SELECT * FROM general_data WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $row = $stmt->rowCount();

        /* hallar valor total de los gastos */

        $stmt = $connection->prepare("SELECT SUM(expense_value) as expenses_value 
                                  FROM expenses WHERE id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $total_expense = $stmt->fetch($connection::FETCH_ASSOC);

        if ($row > 0) {
            /* update */
            $stmt = $connection->prepare("UPDATE general_data SET total_expense = :total_expense 
                                    WHERE id_company = :id_company");
            $stmt->execute(['total_expense' => $total_expense['expenses_value'], 'id_company' => $id_company]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } else {
            /* Inserta */
            $stmt = $connection->prepare("INSERT INTO general_data 
                                    SET total_expense = :total_expense, id_company = :id_company ");
            $stmt->execute(['total_expense' => $total_expense['expenses_value'], 'id_company' => $id_company]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
