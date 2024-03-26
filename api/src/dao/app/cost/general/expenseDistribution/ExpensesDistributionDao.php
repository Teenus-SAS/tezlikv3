<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ExpensesDistributionDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllExpensesDistributionByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT me.id_expenses_distribution, p.id_product, p.reference, p.product, pc.new_product,
                                             me.units_sold, me.turnover, me.assignable_expense, (((me.units_sold / total_units_sold) + (me.turnover / total_turnover)) / 2) * 100 AS participation
                                      FROM expenses_distribution me
                                        INNER JOIN products p ON p.id_product = me.id_product
                                        INNER JOIN products_costs pc ON pc.id_product = me.id_product
                                        INNER JOIN (SELECT expenses_distribution.id_company, SUM(units_sold) AS total_units_sold, SUM(turnover) AS total_turnover 
                                                    FROM expenses_distribution 
                                                        INNER JOIN products_costs ON products_costs.id_product = expenses_distribution.id_product
                                                    WHERE expenses_distribution.id_company = :id_company AND products_costs.new_product = 0 GROUP BY expenses_distribution.id_company) AS totals ON totals.id_company = me.id_company
                                      WHERE me.id_company = :id_company AND p.active = 1");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $expenses = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("expenses", array('expenses' => $expenses));
        return $expenses;
    }

    // Consultar si existe distribucion de gasto en BD
    public function findExpenseDistribution($dataExpensesDistribution, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_expenses_distribution FROM expenses_distribution 
                                      WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => trim($dataExpensesDistribution['selectNameProduct']),
            'id_company' => $id_company
        ]);
        $findExpenseDistribution = $stmt->fetch($connection::FETCH_ASSOC);
        return $findExpenseDistribution;
    }

    public function insertExpensesDistributionByCompany($dataExpensesDistribution, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        // $unitsSold = str_replace('.', '', $dataExpensesDistribution['unitsSold']);
        // if (str_contains($unitsSold, ','))
        //     $unitsSold = str_replace(',', '.', $unitsSold);
        // $turnover = str_replace('.', '', $dataExpensesDistribution['turnover']);
        // if (str_contains($turnover, ','))
        //     $turnover = str_replace(',', '.', $turnover);

        try {
            $stmt = $connection->prepare("INSERT INTO expenses_distribution (id_product, id_company, units_sold, turnover)
                                          VALUES (:id_product, :id_company, :units_sold, :turnover)");
            $stmt->execute([
                'id_product' => trim($dataExpensesDistribution['selectNameProduct']),
                'id_company' => $id_company,
                'units_sold' => $dataExpensesDistribution['unitsSold'],
                'turnover' => $dataExpensesDistribution['turnover']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Distribucion de gastos duplicado. Ingrese una nueva distribucion';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateExpensesDistribution($dataExpensesDistribution)
    {
        $connection = Connection::getInstance()->getConnection();

        // $unitsSold = str_replace('.', '', $dataExpensesDistribution['unitsSold']);
        // if (str_contains($unitsSold, ','))
        //     $unitsSold = str_replace(',', '.', $unitsSold);
        // $turnover = str_replace('.', '', $dataExpensesDistribution['turnover']);
        // if (str_contains($turnover, ','))
        //     $turnover = str_replace(',', '.', $turnover);

        try {
            $stmt = $connection->prepare("UPDATE expenses_distribution SET id_product = :id_product, units_sold = :units_sold, turnover = :turnover
                                          WHERE id_expenses_distribution = :id_expenses_distribution");
            $stmt->execute([
                'id_expenses_distribution' => trim($dataExpensesDistribution['idExpensesDistribution']),
                'id_product' => trim($dataExpensesDistribution['selectNameProduct']),
                'units_sold' => $dataExpensesDistribution['unitsSold'],
                'turnover' => $dataExpensesDistribution['turnover']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteExpensesDistribution($dataExpensesDistribution)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM expenses_distribution WHERE id_expenses_distribution = :id_expenses_distribution");
        $stmt->execute(['id_expenses_distribution' => $dataExpensesDistribution['idExpensesDistribution']]);
        $row = $stmt->rowCount();

        if ($row > 0) {
            $stmt = $connection->prepare("DELETE FROM expenses_distribution WHERE id_expenses_distribution = :id_expenses_distribution");
            $stmt->execute(['id_expenses_distribution' => $dataExpensesDistribution['idExpensesDistribution']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
