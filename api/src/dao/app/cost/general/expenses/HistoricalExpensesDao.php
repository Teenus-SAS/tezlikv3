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
            $sql = "SELECT * FROM tezlikso_HistProduccion.historical_expenses 
                    WHERE year = :year AND month = :month AND id_puc = :id_puc AND id_company = :id_company";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'year' => $dataExpense['year'],
                'month' => $dataExpense['month'],
                'id_puc' => $dataExpense['id_puc'],
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
            $sql = "INSERT INTO tezlikso_HistProduccion.historical_expenses (id_company, year, month, id_puc, expense_value, participation) 
                    VALUES (:id_company, :year, :month, :id_puc, :expense_value, :participation)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_company' => $id_company,
                'year' => $dataExpense['year'],
                'month' => $dataExpense['month'],
                'id_puc' => $dataExpense['id_puc'],
                'expense_value' => $dataExpense['expense_value'],
                'participation' => $dataExpense['participation']
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
            $sql = "UPDATE tezlikso_HistProduccion.historical_expenses SET expense_value = :expense_value, participation = :participation
                    WHERE id_historical_expense = :id_historical_expense";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_historical_expense' => $dataExpense['id_historical_expense'],
                'expense_value' => $dataExpense['expense_value'],
                'participation' => $dataExpense['participation']
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

    public function deleteSoftHistoricalExpense(int $id_company, int $id_user, array $data, $connection = null)
    {
        $useExternalConnection = $connection !== null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "UPDATE tezlikso_HistProduccion.historical_expenses SET deleted_at = :deleted_at, deleted_by = :deleted_by 
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
