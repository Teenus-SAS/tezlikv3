<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ExpensesAnualDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllExpensesAnualByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT (SELECT CONCAT(cp.number_count, ' - ', cp.count) FROM puc cp WHERE cp.number_count = (SUBSTRING(p.number_count, 1, 2))) AS puc,
                                      e.id_expense_anual, e.id_puc, p.number_count, p.count, e.participation, e.expense_value
                                  FROM expenses_anual e 
                                  INNER JOIN puc p ON e.id_puc = p.id_puc 
                                  WHERE e.id_company = :id_company 
                                  ORDER BY CAST(SUBSTRING(p.number_count, 1, 2) AS UNSIGNED), CAST(SUBSTRING(p.number_count, 1, 4) AS UNSIGNED), CAST(SUBSTRING(p.number_count, 1, 5) AS UNSIGNED)");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $expenses = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("expenses", array('expenses' => $expenses));
    return $expenses;
  }

  // Consultar si existe el gasto en BD
  public function findExpense($dataExpense, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT id_expense_anual FROM expenses_anual WHERE id_puc = :id_puc AND id_company = :id_company");
    $stmt->execute([
      'id_puc' => trim($dataExpense['idPuc']),
      'id_company' => $id_company
    ]);
    $findExpense = $stmt->fetch($connection::FETCH_ASSOC);
    return $findExpense;
  }

  public function insertExpensesAnualByCompany($dataExpense, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("INSERT INTO expenses_anual (id_puc, id_company, expense_value)
                                    VALUES (:id_puc, :id_company, :expense_value)");
      $stmt->execute([
        'id_puc' => trim($dataExpense['idPuc']),
        'id_company' => $id_company,
        'expense_value' => trim($dataExpense['expenseValue'])
      ]);

      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function updateExpensesAnual($dataExpense)
  {
    $connection = Connection::getInstance()->getConnection();
    // $expenseValue = str_replace('.', '', $dataExpense['expenseValue']);

    try {
      $stmt = $connection->prepare("UPDATE expenses_anual SET id_puc = :id_puc, expense_value = :expense_value
                                      WHERE id_expense_anual = :id_expense_anual");
      $stmt->execute([
        'id_puc' => trim($dataExpense['idPuc']),
        'expense_value' => trim($dataExpense['expenseValue']),
        // 'id_production_center' => $dataExpense['production'],
        'id_expense_anual' => trim($dataExpense['idExpense'])
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteExpensesAnual($id_expense_anual)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT * FROM expenses_anual WHERE id_expense_anual = :id_expense_anual");
    $stmt->execute(['id_expense_anual' => $id_expense_anual]);
    $row = $stmt->rowCount();

    if ($row > 0) {
      $stmt = $connection->prepare("DELETE FROM expenses_anual WHERE id_expense_anual = :id_expense_anual");
      $stmt->execute(['id_expense_anual' => $id_expense_anual]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }
}
