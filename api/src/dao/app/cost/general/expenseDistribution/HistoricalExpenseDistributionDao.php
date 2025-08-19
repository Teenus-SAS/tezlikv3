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
            $sql = "SELECT * FROM tezlikso_HistProduccion.historical_expense_distribution 
                    WHERE year = :year AND month = :month AND id_product = :id_product AND id_company = :id_company";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'year' => $dataExpense['year'],
                'month' => $dataExpense['month'],
                'id_product' => $dataExpense['id_product'],
                'id_company' => $id_company
            ]);


            $expense = $stmt->fetch($connection::FETCH_ASSOC);
            return $expense;
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function insertHistoricalExpense($dataExpense, $id_company, $connection = null)
    {
        $useExternalConnection = $connection !== null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "INSERT INTO tezlikso_HistProduccion.historical_expense_distribution (id_company, year, month, id_product, units_sold, turnover, assignable_expense) 
                    VALUES (:id_company, :year, :month, :id_product, :units_sold, :turnover, :assignable_expense)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_company' => $id_company,
                'year' => $dataExpense['year'],
                'month' => $dataExpense['month'],
                'id_product' => $dataExpense['id_product'],
                'units_sold' => $dataExpense['units_sold'],
                'turnover' => $dataExpense['turnover'],
                'assignable_expense' => $dataExpense['assignable_expense']
            ]);
        } catch (\PDOException $e) {
            $this->logger->critical(__FUNCTION__ . ': Error de base de datos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => true, 'message' => 'Error al insertar los datos históricos'];
            if (!$useExternalConnection && isset($connection)) {
                $connection->rollBack();
            }
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__ . ': Error general', [
                'error' => $e->getMessage()
            ]);
            return ['error' => true, 'message' => $e->getMessage()];
        } finally {
            if (!$useExternalConnection && isset($connection)) {
                $connection = null;
            }
        }
    }

    public function updateHistoricalExpense($dataExpense, $connection = null)
    {
        $useExternalConnection = $connection !== null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "UPDATE tezlikso_HistProduccion.historical_expense_distribution SET units_sold = :units_sold, turnover = :turnover, assignable_expense = :assignable_expense
                    WHERE id_historical_distribution = :id_historical_distribution";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_historical_distribution' => $dataExpense['id_historical_distribution'],
                'units_sold' => $dataExpense['units_sold'],
                'turnover' => $dataExpense['turnover'],
                'assignable_expense' => $dataExpense['assignable_expense']
            ]);
        } catch (\PDOException $e) {
            $this->logger->critical(__FUNCTION__ . ': Error de base de datos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => true, 'message' => 'Error al insertar los datos históricos'];
            if (!$useExternalConnection && isset($connection)) {
                $connection->rollBack();
            }
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__ . ': Error general', [
                'error' => $e->getMessage()
            ]);
            return ['error' => true, 'message' => $e->getMessage()];
        } finally {
            if (!$useExternalConnection && isset($connection)) {
                $connection = null;
            }
        }
    }

    public function deleteSoftHistoricalExpenseDistribution(int $id_company, int $id_user, array $data, $connection = null)
    {
        $useExternalConnection = $connection !== null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "DELETE tezlikso_HistProduccion.historical_expenses_distribution
                    WHERE id_company = :id_company AND year = :year AND month = :month AND deleted_at IS NULL";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_company' => $id_company,
                'year' => $data['year'],
                'month' => $data['month'],
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => $id_user,
            ]);
        } catch (\PDOException $e) {
            $this->logger->critical(__FUNCTION__ . ': Error de base de datos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => true, 'message' => 'Error al actualizar los datos históricos'];
            if (!$useExternalConnection && isset($connection)) {
                $connection->rollBack();
            }
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__ . ': Error general', [
                'error' => $e->getMessage()
            ]);
            return ['error' => true, 'message' => $e->getMessage()];
        } finally {
            if (!$useExternalConnection && isset($connection)) {
                $connection = null;
            }
        }
    }
}
