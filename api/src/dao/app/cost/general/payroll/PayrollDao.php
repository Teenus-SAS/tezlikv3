<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PayrollDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllPayrollByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    /*
      $stmt = $connection->prepare("SELECT p.id_payroll, p.id_process, p.id_company, p.employee, p.salary, p.transport, p.extra_time, p.bonification, p.endowment, p.working_days_month, p.hours_day, 
                                          p.factor_benefit, p.salary_net, p.type_contract, p.minute_value, pp.process, p.id_risk, rk.risk_level, IFNULL(rk.percentage, 0) AS percentage, 
                                          IF(cl.flag_employee = 1, IFNULL((SELECT id_product_process FROM products_process WHERE employee LIKE CONCAT('%', p.id_payroll, '%')LIMIT 1), 0), 0) AS status_pp,
                                          IFNULL((SELECT COUNT(id_product_process) FROM products_process WHERE employee LIKE CONCAT('%', p.id_payroll, '%')), 0) AS count_pp
                                    FROM payroll p 
                                      INNER JOIN process pp ON p.id_process = pp.id_process
                                      LEFT JOIN risks rk ON rk.id_risk = p.id_risk
                                      INNER JOIN companies_licenses cl ON cl.id_company = p.id_company 
                                    WHERE p.id_company = :id_company
                                    ORDER BY p.route ASC;");
    */
    $stmt = $connection->prepare("SELECT p.id_payroll, p.id_process, p.id_company, p.employee, p.salary, p.transport, p.extra_time, p.bonification, p.endowment, p.working_days_month, p.hours_day, p.factor_benefit, p.salary_net, 
                                         p.type_contract, p.minute_value, pp.process, p.id_risk, rk.risk_level, IFNULL(rk.percentage, 0) AS percentage, IF(cl.flag_employee = 1, IFNULL(ppp.id_product_process, 0), 0) AS id_product_process, IFNULL(ppp.count_pp, 0) AS count_pp
                                  FROM payroll p 
                                    INNER JOIN process pp ON p.id_process = pp.id_process
                                    LEFT JOIN risks rk ON rk.id_risk = p.id_risk
                                    INNER JOIN companies_licenses cl ON cl.id_company = p.id_company 
                                    LEFT JOIN 
                                      (SELECT 
                                          employee, 
                                          GROUP_CONCAT(id_product_process) AS id_product_process, 
                                          COUNT(id_product_process) AS count_pp 
                                      FROM products_process 
                                      GROUP BY employee) 
                                    ppp ON ppp.employee LIKE CONCAT('%', p.id_payroll, '%')
                                  WHERE p.id_company = :id_company
                                  GROUP BY 
                                    p.id_payroll, p.id_process, p.id_company, p.employee, p.salary, p.transport, p.extra_time, p.bonification, p.endowment, p.working_days_month, p.hours_day, 
                                    p.factor_benefit, p.salary_net, p.type_contract, p.minute_value, pp.process, p.id_risk, rk.risk_level, rk.percentage, cl.flag_employee, ppp.id_product_process
                                  ORDER BY p.route ASC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $payroll = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("payroll", array('payroll' => $payroll));
    return $payroll;
  }

  public function insertPayrollByCompany($dataPayroll, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("INSERT INTO payroll (id_company, id_process, employee, salary, transport, extra_time, bonification, endowment,
                                                        working_days_month, hours_day, factor_benefit, id_risk, salary_net, type_contract, minute_value)
                                    VALUES (:id_company, :id_process, :employee, :salary, :transport, :extra_time, :bonification, :endowment,
                                            :working_days_month, :hours_day, :factor_benefit, :id_risk, :salary_net, :type_contract, :minute_value)");
      $stmt->execute([
        'id_company' => $id_company,                                      'employee' => strtoupper(trim($dataPayroll['employee'])),
        'id_process' => trim($dataPayroll['idProcess']),                  'salary' => trim($dataPayroll['basicSalary']),
        'transport' => trim($dataPayroll['transport']),                   'extra_time' => trim($dataPayroll['extraTime']),
        'bonification' => trim($dataPayroll['bonification']),             'endowment' => trim($dataPayroll['endowment']),
        'working_days_month' => trim($dataPayroll['workingDaysMonth']),   'hours_day' => trim($dataPayroll['workingHoursDay']),
        'factor_benefit' => trim($dataPayroll['factor']),                 'type_contract' => ucfirst(strtolower(trim($dataPayroll['typeFactor']))),
        'id_risk' => trim($dataPayroll['risk']),                          'minute_value' => trim($dataPayroll['minuteValue']),
        'salary_net' => trim($dataPayroll['salaryNet'])
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      if ($e->getCode() == 23000)
        $message = 'Registro duplicado. Ingrese una nuevo Registro';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function updatePayroll($dataPayroll)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("UPDATE payroll SET employee=:employee, id_process=:id_process, salary=:salary, transport=:transport, extra_time=:extra_time,
                                            bonification=:bonification, endowment=:endowment, working_days_month=:working_days_month,
                                            hours_day=:hours_day, factor_benefit=:factor_benefit, id_risk = :id_risk, salary_net= :salary_net, type_contract=:type_contract, minute_value=:minute_value
                                    WHERE id_payroll = :id_payroll");
      $stmt->execute([
        'id_payroll' => trim($dataPayroll['idPayroll']),                  'employee' => strtoupper(trim($dataPayroll['employee'])),
        'id_process' => trim($dataPayroll['idProcess']),                  'salary' => trim($dataPayroll['basicSalary']),
        'transport' => trim($dataPayroll['transport']),                   'extra_time' => trim($dataPayroll['extraTime']),
        'bonification' => trim($dataPayroll['bonification']),             'endowment' => trim($dataPayroll['endowment']),
        'working_days_month' => trim($dataPayroll['workingDaysMonth']),   'hours_day' => trim($dataPayroll['workingHoursDay']),
        'factor_benefit' => trim($dataPayroll['factor']),                 'type_contract' => ucfirst(strtolower(trim($dataPayroll['typeFactor']))),
        'id_risk' => trim($dataPayroll['risk']),                          'minute_value' => trim($dataPayroll['minuteValue']),
        'salary_net' => trim($dataPayroll['salaryNet']),
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deletePayroll($id_payroll)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT * FROM payroll WHERE id_payroll = :id_payroll");
    $stmt->execute(['id_payroll' => $id_payroll]);
    $rows = $stmt->rowCount();

    if ($rows > 0) {
      $stmt = $connection->prepare("DELETE FROM payroll WHERE id_payroll = :id_payroll");
      $stmt->execute(['id_payroll' => $id_payroll]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }
}
