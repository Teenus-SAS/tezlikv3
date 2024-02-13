<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class HistoricalExpenseDistributionDao
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
            $stmt = $connection->prepare("SELECT * FROM historical_expense_distribution 
                                      WHERE year = :year AND month = :month AND id_product = :id_product AND id_company = :id_company");
            $stmt->execute([
                'year' => $dataExpense['year'],
                'month' => $dataExpense['month'],
                'id_product' => $dataExpense['id_product'],
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
            $stmt = $connection->prepare("INSERT INTO historical_expense_distribution (id_company, year, month, id_product, units_sold, turnover, assignable_expense) 
                                      VALUES (:id_company, :year, :month, :id_product, :units_sold, :turnover, :assignable_expense)");
            $stmt->execute([
                'id_company' => $id_company,
                'year' => $dataExpense['year'],
                'month' => $dataExpense['month'],
                'id_product' => $dataExpense['id_product'],
                'units_sold' => $dataExpense['units_sold'],
                'turnover' => $dataExpense['turnover'],
                'assignable_expense' => $dataExpense['assignable_expense']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updateHistoricalExpense($dataExpense)
    {
        $connection = Connection::getInstance()->getConnection1();
        try {
            // id_product = :id_product,
            $stmt = $connection->prepare("UPDATE historical_expense_distribution SET units_sold = :units_sold, turnover = :turnover, assignable_expense = :assignable_expense
                                          WHERE id_historical_distribution = :id_historical_distribution");
            $stmt->execute([
                'id_historical_distribution' => $dataExpense['id_historical_distribution'],
                'units_sold' => $dataExpense['units_sold'],
                'turnover' => $dataExpense['turnover'],
                'assignable_expense' => $dataExpense['assignable_expense']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
