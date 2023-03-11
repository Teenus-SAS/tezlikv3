<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DashboardGeneralDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  // Buscar tiempos procesos
  public function findTimeProcessForProductByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.product, IFNULL((SUM(pp.enlistment_time) + SUM(pp.operation_time)), 0) AS totalTime
                                      FROM products p
                                        LEFT JOIN products_process pp ON pp.id_product = p.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1
                                      GROUP BY p.product ORDER BY `totalTime` DESC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $timeProcess = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("timeProcess", array('timeProcess' => $timeProcess));
    return $timeProcess;
  }

  // Buscar promedio tiempos procesos
  public function findAverageTimeProcessByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.product, IFNULL(pp.enlistment_time, 0) AS enlistment_time, IFNULL(pp.operation_time, 0) AS operation_time
                                      FROM products p
                                        LEFT JOIN products_process pp ON pp.id_product = p.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1
                                      GROUP BY pp.id_product_process
                                      ORDER BY `p`.`product` ASC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $averageTimeProcess = $stmt->fetchAll($connection::FETCH_ASSOC);

    $this->logger->notice("averageTimeProcess", array('averageTimeProcess' => $averageTimeProcess));
    return $averageTimeProcess;
  }

  public function findProcessMinuteValueByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT pc.process, (SELECT SUM(minute_value) FROM payroll WHERE id_process = py.id_process) AS minute_value
                                      FROM process pc
                                        INNER JOIN payroll py ON py.id_process = pc.id_process
                                      WHERE pc.id_company = :id_company GROUP BY `py`.`id_process`");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $processMinuteValue = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("processMinuteValue", array('processMinuteValue' => $processMinuteValue));
    return $processMinuteValue;
  }

  public function findFactoryLoadMinuteValueByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT m.machine, SUM(ml.cost_minute) AS totalCostMinute
                                      FROM machines m 
                                      INNER JOIN manufacturing_load ml ON ml.id_machine = m.id_machine 
                                      WHERE ml.id_company = :id_company GROUP BY m.machine 
                                      ORDER BY `totalCostMinute` ASC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $factoryLoadMinuteValue = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("factoryLoadMinuteValue", array('factoryLoadMinuteValue' => $factoryLoadMinuteValue));
    return $factoryLoadMinuteValue;
  }

  public function findExpensesDistributionValueByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    // Contar todos los productos
    $stmt = $connection->prepare("SELECT COUNT(p.product) AS products
                                      FROM products p
                                        INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1");
    $stmt->execute(['id_company' => $id_company]);
    $quantityProducts = $stmt->fetch($connection::FETCH_ASSOC);


    for ($i = 1; $i < 4; $i++) {
      $stmt = $connection->prepare("SELECT p.number_count, SUM(ex.expense_value) AS expenseCount
                                      FROM expenses ex
                                      LEFT JOIN puc p ON p.id_puc = ex.id_puc
                                      WHERE ex.id_company = :id_company AND
                                      p.number_count LIKE '5{$i}%'");
      $stmt->execute(['id_company' => $id_company]);
      $expenseCount = $stmt->fetch($connection::FETCH_ASSOC);
      $expenseValue[$i] =  $expenseCount;
    }
    $expenseValue = array_merge($expenseValue, $quantityProducts);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $this->logger->notice("expenseValue", array('expenseValue' => $expenseValue));
    return $expenseValue;
  }

  public function findExpensesRecoverValueByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT SUM(er.expense_recover) / COUNT(p.id_product) AS percentageExpense
                                      FROM products p
                                        INNER JOIN expenses_recover er ON er.id_product = p.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1");
    $stmt->execute(['id_company' => $id_company]);
    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    $expenseValue = $stmt->fetch($connection::FETCH_ASSOC);

    return $expenseValue;
  }

  //CONTAR MATERIAS PRIMA
  public function findRawMaterialsByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    // Contar todos los productos
    $stmt = $connection->prepare("SELECT COUNT(m.material) AS materials 
                                      FROM materials m
                                        INNER JOIN convert_units u ON u.id_unit = m.unit
                                      WHERE m.id_company = :id_company");
    $stmt->execute(['id_company' => $id_company]);
    $quantityMaterials = $stmt->fetch($connection::FETCH_ASSOC);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    $this->logger->notice("expenseValue", array('expenseValue' => $quantityMaterials));
    return $quantityMaterials;
  }
}
