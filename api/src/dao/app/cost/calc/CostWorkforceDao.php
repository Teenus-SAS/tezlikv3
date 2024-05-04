<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CostWorkforceDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Buscar producto por el idProcess
    public function findProductByProcess($idProcess, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pp.id_product_process, pp.id_product, pp.employee
                                      FROM products_process pp
                                        INNER JOIN products p ON p.id_product = pp.id_product
                                      WHERE pp.id_process = :id_process AND pp.id_company = :id_company");
        $stmt->execute(['id_process' => $idProcess, 'id_company' => $id_company]);
        $dataProduct = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $dataProduct;
    }

    // Buscar costo de nomina y modificar en products_process
    public function calcCostPayroll($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT pp.id_product_process, SUM(IFNULL(p.minute_value, 0) * (pp.enlistment_time + pp.operation_time)) AS cost, pp.auto_machine
                                          FROM products_process pp 
                                            LEFT JOIN payroll p ON p.id_process = pp.id_process 
                                          WHERE pp.id_product = :id_product AND pp.id_company = :id_company GROUP BY `pp`.`id_product_process`");
            $stmt->execute([
                'id_product' => $idProduct,
                'id_company' => $id_company
            ]);
            $payroll = $stmt->fetchAll($connection::FETCH_ASSOC);

            for ($i = 0; $i < sizeof($payroll); $i++) {
                $cost = $payroll[$i]['cost'];
                if ($payroll[$i]['auto_machine'] == '1') $cost = 0;

                $this->updateTotalCostWorkforceByProductProcess($cost, $payroll[$i]['id_product_process']);
                // $stmt = $connection->prepare("UPDATE products_process SET workforce_cost = :workforce_cost WHERE id_product_process = :id_product_process");
                // $stmt->execute([
                //     'workforce_cost' => $payroll[$i]['cost'],
                //     'id_product_process' => $payroll[$i]['id_product_process']
                // ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $payroll = array('info' => true, 'message' => $message);
        }
    }

    // Calcular MO Inyeccion
    public function calcCostPayrollInyection($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT pp.id_product_process, SUM(p.minute_value * (m.unity_time / (pp.operation_time / 100))) AS cost, pp.auto_machine
                                          FROM products_process pp 
                                            INNER JOIN payroll p ON p.id_process = pp.id_process 
                                            INNER JOIN machines m ON m.id_machine = pp.id_machine 
                                          WHERE pp.id_product = :id_product AND pp.id_company = :id_company GROUP BY `pp`.`id_product_process`");
            $stmt->execute([
                'id_product' => $idProduct,
                'id_company' => $id_company
            ]);
            $payroll = $stmt->fetchAll($connection::FETCH_ASSOC);

            for ($i = 0; $i < sizeof($payroll); $i++) {
                $cost = $payroll[$i]['cost'];
                if ($payroll[$i]['auto_machine'] == '1') $cost = 0;

                $this->updateTotalCostWorkforceByProductProcess($cost, $payroll[$i]['id_product_process']);

                // $stmt = $connection->prepare("UPDATE products_process SET workforce_cost = :workforce_cost WHERE id_product_process = :id_product_process");
                // $stmt->execute([
                //     'workforce_cost' => $payroll[$i]['cost'],
                //     'id_product_process' => $payroll[$i]['id_product_process']
                // ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $payroll = array('info' => true, 'message' => $message);
        }
    }

    public function calcCostPayrollInyectionGroupEmployee($idProduct, $employees)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT pp.id_product_process, SUM(p.minute_value * (m.unity_time / (pp.operation_time / 100))) AS cost
                                          FROM products_process pp 
                                            INNER JOIN payroll p ON p.id_process = pp.id_process 
                                            INNER JOIN machines m ON m.id_machine = pp.id_machine 
                                          WHERE pp.id_product = :id_product AND p.id_payroll IN ($employees)
                                          GROUP BY `pp`.`id_product_process`");
            $stmt->execute([
                'id_product' => $idProduct
            ]);
            $payroll = $stmt->fetchAll($connection::FETCH_ASSOC);

            for ($i = 0; $i < sizeof($payroll); $i++) {
                $this->updateTotalCostWorkforceByProductProcess($payroll[$i]['cost'], $payroll[$i]['id_product_process']);

                // $stmt = $connection->prepare("UPDATE products_process SET workforce_cost = :workforce_cost WHERE id_product_process = :id_product_process");
                // $stmt->execute([
                //     'workforce_cost' => $payroll[$i]['cost'],
                //     'id_product_process' => $payroll[$i]['id_product_process']
                // ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $payroll = array('info' => true, 'message' => $message);
        }
    }

    public function calcCostPayrollGroupByEmployee($idProduct, $id_company, $employees)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT pp.id_product_process, SUM(p.minute_value * (pp.enlistment_time + pp.operation_time)) AS cost
                                          FROM products_process pp 
                                            INNER JOIN payroll p ON p.id_process = pp.id_process 
                                          WHERE pp.id_product = :id_product AND pp.id_company = :id_company AND p.id_payroll IN ($employees)
                                          GROUP BY `pp`.`id_product_process`");
            $stmt->execute([
                'id_product' => $idProduct,
                'id_company' => $id_company
            ]);
            $payroll = $stmt->fetchAll($connection::FETCH_ASSOC);

            for ($i = 0; $i < sizeof($payroll); $i++) {
                $this->updateTotalCostWorkforceByProductProcess($payroll[$i]['cost'], $payroll[$i]['id_product_process']);
                // $stmt = $connection->prepare("UPDATE products_process SET workforce_cost = :workforce_cost WHERE id_product_process = :id_product_process");
                // $stmt->execute([
                //     'workforce_cost' => $payroll[$i]['cost'],
                //     'id_product_process' => $payroll[$i]['id_product_process']
                // ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $payroll = array('info' => true, 'message' => $message);
        }
    }

    // Buscar costo de nomina
    public function calcTotalCostPayroll($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT IFNULL(SUM(IFNULL(p.minute_value, 0) * (IFNULL(pp.enlistment_time, 0) + IFNULL(pp.operation_time, 0))), 0) AS cost
                                          FROM products_process pp 
                                            INNER JOIN payroll p ON p.id_process = pp.id_process AND pp.auto_machine = 0
                                          WHERE pp.id_product = :id_product AND pp.id_company = :id_company");
            $stmt->execute([
                'id_product' => $idProduct,
                'id_company' => $id_company
            ]);
            $payroll = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $payroll = array('info' => true, 'message' => $message);
        }
        return $payroll;
    }

    public function calcTotalCostPayrollGroupByEmployee($idProduct, $id_company, $employees)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT SUM(p.minute_value * (pp.enlistment_time + pp.operation_time)) AS cost
                                          FROM products_process pp 
                                            INNER JOIN payroll p ON p.id_process = pp.id_process 
                                          WHERE pp.id_product = :id_product AND pp.id_company = :id_company AND p.id_payroll IN($employees)");
            $stmt->execute([
                'id_product' => $idProduct,
                'id_company' => $id_company
            ]);
            $payroll = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $payroll = array('info' => true, 'message' => $message);
        }
        return $payroll;
    }

    // Modificar costo de nomina en product_process x producto
    public function updateTotalCostWorkforceByProductProcess($costPayroll, $idProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_process SET workforce_cost = :workforce_cost WHERE id_product_process = :id_product_process");
            $stmt->execute([
                'workforce_cost' => $costPayroll,
                'id_product_process' => $idProductProcess
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
    // Modificar costo de nomina en products_costs
    public function updateTotalCostWorkforce($costPayroll, $idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_costs SET cost_workforce = :workforce WHERE id_product = :id_product AND id_company = :id_company");
            $stmt->execute([
                'workforce' => $costPayroll,
                'id_product' => $idProduct,
                'id_company' => $id_company
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
