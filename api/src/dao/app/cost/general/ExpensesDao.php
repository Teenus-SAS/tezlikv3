<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ExpensesDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllExpensesByCompany()
  {
    session_start();
    $id_company = $_SESSION['id_company'];
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT e.id_expense, e.id_puc, p.number_count, p.count, e.expense_value 
                                  FROM expenses e 
                                  INNER JOIN puc p ON e.id_puc = p.id_puc 
                                  WHERE e.id_company = :id_company;");
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

    $stmt = $connection->prepare("SELECT id_expense FROM expenses WHERE id_puc = :id_puc AND id_company = :id_company");
    $stmt->execute([
      'id_puc' => trim($dataExpense['idPuc']),
      'id_company' => $id_company
    ]);
    $findExpense = $stmt->fetch($connection::FETCH_ASSOC);
    return $findExpense;
  }

  public function insertExpensesByCompany($dataExpense, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $expenseValue = str_replace('.', '', $dataExpense['expenseValue']);

    try {
      $stmt = $connection->prepare("INSERT INTO expenses (id_puc, id_company, expense_value)
                                    VALUES (:id_puc, :id_company, :expense_value)");
      $stmt->execute([
        'id_puc' => trim($dataExpense['idPuc']),
        'id_company' => $id_company,
        'expense_value' => trim($expenseValue)
      ]);

      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      if ($e->getCode() == 23000)
        $message = 'No. Cuenta duplicada. Ingrese un nuevo No. Cuenta';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function updateExpenses($dataExpense)
  {
    $connection = Connection::getInstance()->getConnection();
    $expenseValue = str_replace('.', '', $dataExpense['expenseValue']);

    try {
      $stmt = $connection->prepare("UPDATE expenses SET id_puc = :id_puc, expense_value = :expense_value
                                      WHERE id_expense = :id_expense");
      $stmt->execute([
        'id_puc' => trim($dataExpense['idPuc']),
        'expense_value' => trim($expenseValue),
        'id_expense' => trim($dataExpense['idExpense'])
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteExpenses($id_expense)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT * FROM expenses WHERE id_expense = :id_expense");
    $stmt->execute(['id_expense' => $id_expense]);
    $row = $stmt->rowCount();

    if ($row > 0) {
      $stmt = $connection->prepare("DELETE FROM expenses WHERE id_expense = :id_expense");
      $stmt->execute(['id_expense' => $id_expense]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }
}
