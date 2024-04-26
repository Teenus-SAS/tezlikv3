<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsProcessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsprocess($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pp.id_product_process, p.id_product, pc.id_process, p.reference, p.product, pp.enlistment_time, pp.operation_time, IFNULL(mc.id_machine, 0) AS id_machine, IFNULL(mc.machine, 'PROCESO MANUAL') AS machine, pc.process,
                                             pp.workforce_cost, pp.indirect_cost, pp.employee, pp.route, IF(pp.auto_machine = 0, 'NO','SI') AS auto_machine,
                                             (SELECT COUNT(cpp.id_product_process)
                                                FROM products_process cpp
                                                INNER JOIN payroll cp ON cp.id_process = cpp.id_process
                                                WHERE cpp.id_product_process = pp.id_product_process) AS count_employee
                                  FROM products p 
                                  INNER JOIN products_process pp ON pp.id_product = p.id_product
                                  LEFT JOIN machines mc ON mc.id_machine = pp.id_machine 
                                  INNER JOIN process pc ON pc.id_process = pp.id_process
                                  LEFT JOIN payroll py ON py.id_process = pp.id_process
                                  WHERE p.id_company = :id_company 
                                  GROUP BY pp.id_product_process
                                  ORDER BY pp.route ASC");
        $stmt->execute(['id_company' => $id_company]);
        $productsprocess = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsprocess));
        return $productsprocess;
    }

    // Consultar datos del prodcuto en la BD
    public function findProductProcessByIdProduct($dataProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_process WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $dataProductProcess['idOldProduct']
        ]);
        $findProductProcess = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $findProductProcess;
    }

    public function findProductProcessByIdMachine($id_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_process WHERE id_machine = :id_machine");
        $stmt->execute([
            'id_machine' => $id_machine
        ]);
        $findProductProcess = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $findProductProcess;
    }

    public function findAllEmloyeesByProcess($id_product_process)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_payroll, p.employee
                                      FROM products_process pp
                                      INNER JOIN payroll p ON p.id_process = pp.id_process
                                      WHERE pp.id_product_process = :id_product_process");
        $stmt->execute([
            'id_product_process' => $id_product_process
        ]);
        $employees = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $employees;
    }

    public function findNextRouteByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(route) + 1 AS route
                                      FROM products_process
                                      WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product,
        ]);
        $productsProcess = $stmt->fetch($connection::FETCH_ASSOC);
        return $productsProcess;
    }

    public function changeRouteById($id_product_process, $route)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE products_process SET route = :route WHERE id_product_process = :id_product_process");
            $stmt->execute([
                'route' => $route,
                'id_product_process' => $id_product_process
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updateEmployees($id_product_process, $employees)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE products_process SET employee = :employee WHERE id_product_process = :id_product_process");
            $stmt->execute([
                'id_product_process' => $id_product_process,
                'employee' => $employees
            ]);
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }

    public function deleteProductProcessByProduct($dataProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_process WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $dataProductProcess['idProduct']]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM products_process WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $dataProductProcess['idProduct']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
