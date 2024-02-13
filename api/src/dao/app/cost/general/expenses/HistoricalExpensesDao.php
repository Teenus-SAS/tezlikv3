<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class HistoricalExpensesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findHistorical($dataExpense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection1();
        try {
            $stmt = $connection->prepare("SELECT * FROM historical_expenses 
                                      WHERE year = :year AND month = :month AND id_puc = :id_puc AND id_company = :id_company");
            $stmt->execute([
                'year' => $dataExpense['year'],
                'month' => $dataExpense['month'],
                'id_puc' => $dataExpense['id_puc'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

            $expense = $stmt->fetch($connection::FETCH_ASSOC);
            return $expense;
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function insertHistoricalExpense($dataExpense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection1();
        try {
            $stmt = $connection->prepare("INSERT INTO historical_expenses (id_company, year, month, id_puc, expense_value, participation) 
                                      VALUES (:id_company, :year, :month, :id_puc, :expense_value, :participation)");
            $stmt->execute([
                'id_company' => $id_company,
                'year' => $dataExpense['year'],
                'month' => $dataExpense['month'],
                'id_puc' => $dataExpense['id_puc'],
                'expense_value' => $dataExpense['expense_value'],
                'participation' => $dataExpense['participation']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updateHistoricalExpense($dataExpense)
    {
        $connection = Connection::getInstance()->getConnection1();
        try {
            $stmt = $connection->prepare("UPDATE historical_expenses SET expense_value = :expense_value, participation = :participation
                                          WHERE id_historical_expense = :id_historical_expense");
            $stmt->execute([
                'id_historical_expense' => $dataExpense['id_historical_expense'],
                'expense_value' => $dataExpense['expense_value'],
                'participation' => $dataExpense['participation']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
