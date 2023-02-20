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

        $stmt = $connection->prepare("SELECT pp.id_product
                                      FROM products_process pp
                                      WHERE pp.id_process = :id_process AND pp.id_company = :id_company");
        $stmt->execute(['id_process' => $idProcess, 'id_company' => $id_company]);
        $dataProduct = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $dataProduct;
    }

    // Buscar costo de nomina y modificar en products_costs
    public function findCostPayrollAndModify($dataPayroll, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT SUM(p.minute_value * (pp.enlistment_time + pp.operation_time)) AS cost
                                        FROM products_process pp 
                                        INNER JOIN payroll p ON p.id_process = pp.id_process 
                                        WHERE pp.id_product = :id_product AND pp.id_company = :id_company");
            $stmt->execute([
                'id_product' => $dataPayroll['idProduct'],
                'id_company' => $id_company
            ]);
            $payroll = $stmt->fetch($connection::FETCH_ASSOC);

            $dataPayroll['costPayroll'] = $payroll['cost'];
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $dataPayroll = array('info' => true, 'message' => $message);
            return $dataPayroll;
        }
        return $dataPayroll;
    }

    public function updateCostWorkforce($dataPayroll, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_costs SET cost_workforce = :workforce WHERE id_product = :id_product AND id_company = :id_company");
            $stmt->execute([
                'workforce' => $dataPayroll['costPayroll'],
                'id_product' => $dataPayroll['idProduct'],
                'id_company' => $id_company
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    /*
    // General
    public function calcCostPayroll($dataProductProcess, $id_company)
    {
        $this->findCostPayrollAndModify($dataProductProcess['idProduct'], $id_company);
    }

    // Al modificar la nomina
    public function calcCostPayrollByPayroll($dataPayroll, $id_company)
    {
        $dataProduct = $this->findProductByProcess($dataPayroll['idProcess'], $id_company);

        for ($i = 0; $i < sizeof($dataProduct); $i++) {
            $this->findCostPayrollAndModify($dataProduct[$i]['id_product'], $id_company);
        }
    }
    */
}
