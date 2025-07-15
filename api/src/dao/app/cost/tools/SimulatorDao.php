<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class SimulatorDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllFactoryLoadByProduct($id_product, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT ml.id_manufacturing_load, ml.id_machine, m.machine, ml.input, ml.cost, ml.cost_minute 
                                      FROM manufacturing_load ml
                                        INNER JOIN machines m ON ml.id_machine = m.id_machine
                                        INNER JOIN products_process pp ON pp.id_machine = ml.id_machine
                                      WHERE pp.id_product = :id_product AND ml.id_company = :id_company");
    $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);



    $factoryLoads = $stmt->fetchAll($connection::FETCH_ASSOC);
    return $factoryLoads;
  }

  public function findAllPayrollByProduct($id_product, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.id_payroll, p.id_process, p.id_company, p.employee, p.salary, p.transport, p.extra_time, p.bonification, p.endowment, p.working_days_month, p.hours_day, p.factor_benefit, p.salary_net, p.type_contract, p.minute_value, pp.process, p.id_risk, rk.percentage
                                  FROM payroll p 
                                    INNER JOIN process pp ON p.id_process = pp.id_process
                                    INNER JOIN products_process fp ON fp.id_process = p.id_process
                                    INNER JOIN risks rk ON rk.id_risk = p.id_risk
                                  WHERE fp.id_product = :id_product AND p.id_company = :id_company GROUP BY p.id_payroll");
    $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);



    $payroll = $stmt->fetchAll($connection::FETCH_ASSOC);
    return $payroll;
  }
}
