<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPayrollDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }
    public function findDataBasicPayrollByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_payroll, id_process, employee
                                      FROM payroll
                                      WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $payroll = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("payroll", array('payroll' => $payroll));
        return $payroll;
    }

    // Consultar si existe la nomina en BD
    public function findPayroll($dataPayroll, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_payroll FROM payroll
                                  WHERE employee = :employee AND id_process = :id_process AND id_company = :id_company");
        $stmt->execute([
            'employee' => strtoupper(trim($dataPayroll['employee'])),
            'id_process' => trim($dataPayroll['idProcess']),
            'id_company' => $id_company
        ]);
        $findPayroll = $stmt->fetch($connection::FETCH_ASSOC);
        return $findPayroll;
    }

    public function findAllProcessByPayroll($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT DISTINCT pay.id_process, p.process 
                                        FROM payroll pay 
                                        INNER JOIN process p ON p.id_process = pay.id_process 
                                        WHERE pay.id_company = :id_company ORDER BY p.process ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $processPayroll = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("processPayroll", array('processPayroll' => $processPayroll));
        return $processPayroll;
    }

    public function findProcessByPayroll($dataPayroll, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        // Obtener id_proceso
        $stmt = $connection->prepare("SELECT DISTINCT pay.id_process FROM payroll pay 
                                      INNER JOIN process p ON p.id_process = pay.id_process 
                                      WHERE p.process = :process AND pay.id_company = :id_company;");
        $stmt->execute([
            'process' => ucfirst(strtolower(trim($dataPayroll['process']))),
            'id_company' => $id_company
        ]);
        $findProcess = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProcess;
    }

    public function findSalarynetByPayroll($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        // Obtener id_proceso
        $stmt = $connection->prepare("SELECT IFNULL(SUM(salary), 0) AS salary, IFNULL(SUM(salary_net), 0) AS salary_net, IFNULL(SUM(minute_value), 0) AS minute_value 
                                      FROM (SELECT salary, salary_net, minute_value FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $payroll = $stmt->fetch($connection::FETCH_ASSOC);
        return $payroll;
    }

    public function findAllProcessByEmployeeNotIn($employee, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM process p WHERE p.id_company = :id_company 
                                      AND p.id_process NOT IN (SELECT id_process FROM payroll WHERE employee = :employee AND id_company = p.id_company)
                                      ORDER BY `p`.`process` ASC");
        $stmt->execute([
            'employee' => $employee,
            'id_company' => $id_company
        ]);
        $process = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $process;
    }

    public function findAllPayrollByEmployee($employee, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM payroll WHERE employee = :employee AND id_company = :id_company");
        $stmt->execute([
            'employee' => $employee,
            'id_company' => $id_company
        ]);
        $payroll = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $payroll;
    }

    public function findNextRoute($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(route) + 1 AS route
                                      FROM payroll
                                      WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $payroll = $stmt->fetch($connection::FETCH_ASSOC);
        return $payroll;
    }

    public function changeRouteById($id_payroll, $route)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE payroll SET route = :route WHERE id_payroll = :id_payroll");
            $stmt->execute([
                'route' => $route,
                'id_payroll' => $id_payroll,
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updatePayroll($dataPayroll)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE payroll SET employee = :employee, id_process = :id_process WHERE id_payroll = :id_payroll");
            $stmt->execute([
                'employee' => $dataPayroll['employee'],
                'id_process' => $dataPayroll['idProcess'],
                'id_payroll' => $dataPayroll['idPayroll'],
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
