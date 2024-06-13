<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsProcessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsprocessByIdProduct($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT 
                                            -- Ides
                                                pp.id_product_process, 
                                                p.id_product, 
                                                pc.id_process, 
                                                IFNULL(mc.id_machine, 0) AS id_machine,
                                            -- Informacion Basica
                                                p.reference, 
                                                p.product, 
                                                pp.enlistment_time, 
                                                pp.efficiency,
                                                pp.operation_time, 
                                                IFNULL(mc.machine, 'PROCESO MANUAL') AS machine, 
                                                pc.process,
                                            -- Costos
                                                pp.workforce_cost, 
                                                pp.indirect_cost, 
                                            -- Otros 
                                                pp.employee, 
                                                pp.route, 
                                                IF(pp.auto_machine = 0, 'NO','SI') AS auto_machine, 
                                                COUNT(DISTINCT py.employee) AS count_employee
                                      FROM  products p 
                                        INNER JOIN products_process pp ON pp.id_product = p.id_product
                                        INNER JOIN process pc ON pc.id_process = pp.id_process
                                        LEFT JOIN machines mc ON mc.id_machine = pp.id_machine 
                                        LEFT JOIN payroll py ON py.id_process = pp.id_process
                                      WHERE p.id_company = :id_company AND p.id_product = :id_product
                                      GROUP BY pp.id_product_process
                                      ORDER BY pp.route ASC");
        $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
        $productsprocess = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsprocess));
        return $productsprocess;
    }

    // Consultar si existe el proceso del prodcuto en la BD
    public function findProductProcess($dataProductProcess, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_product_process FROM products_process 
                                      WHERE id_product = :id_product AND id_process = :id_process 
                                      AND id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataProductProcess['idProduct'],
            'id_process' => $dataProductProcess['idProcess'],
            'id_machine' => $dataProductProcess['idMachine'],
            'id_company' => $id_company
        ]);
        $findProductProcess = $stmt->fetch($connection::FETCH_ASSOC);

        return $findProductProcess;
    }

    // Insertar productos procesos general
    public function insertProductsProcessByCompany($dataProductProcess, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT id_product_process FROM products_process WHERE id_product = :id_product AND id_company = :id_company
                                          AND id_process = :id_process AND id_machine = :id_machine");
            $stmt->execute([
                'id_product' => $dataProductProcess['idProduct'],
                'id_company' => $id_company,
                'id_process' => $dataProductProcess['idProcess'],
                'id_machine' => $dataProductProcess['idMachine'],
            ]);
            $row = $stmt->fetch($connection::FETCH_ASSOC);

            if ($row > 0) {
                return 1;
            } else {
                $stmt = $connection->prepare("INSERT INTO products_process (id_product, id_company, id_process, id_machine, enlistment_time, operation_time, efficiency, employees, auto_machine) 
                                              VALUES (:id_product, :id_company, :id_process, :id_machine, :enlistment_time, :operation_time, :efficiency, :employees, :auto_machine)");
                $stmt->execute([
                    'id_product' => $dataProductProcess['idProduct'],
                    'id_company' => $id_company,
                    'id_process' => $dataProductProcess['idProcess'],
                    'id_machine' => $dataProductProcess['idMachine'],
                    'enlistment_time' => trim($dataProductProcess['enlistmentTime']),
                    'operation_time' => trim($dataProductProcess['operationTime']),
                    'efficiency' => trim($dataProductProcess['efficiency']),
                    'employee' => $dataProductProcess['employees'],
                    'auto_machine' => $dataProductProcess['autoMachine']
                ]);
            }
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // Actualizar productos procesos general
    public function updateProductsProcess($dataProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_process SET id_product = :id_product, id_process = :id_process, id_machine = :id_machine, enlistment_time = :enlistment_time, 
                                                                      employee = :employee, operation_time = :operation_time, efficiency = :efficiency, auto_machine = :auto_machine
                                          WHERE id_product_process = :id_product_process");
            $stmt->execute([
                'id_product_process' => $dataProductProcess['idProductProcess'],
                'id_product' => $dataProductProcess['idProduct'],
                'id_process' => $dataProductProcess['idProcess'],
                'id_machine' => $dataProductProcess['idMachine'],
                'enlistment_time' => trim($dataProductProcess['enlistmentTime']),
                'operation_time' => trim($dataProductProcess['operationTime']),
                'efficiency' => trim($dataProductProcess['efficiency']),
                'employee' => $dataProductProcess['employees'],
                'auto_machine' => $dataProductProcess['autoMachine']
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteProductProcess($dataProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_process WHERE id_product_process = :id_product_process");
        $stmt->execute(['id_product_process' => $dataProductProcess['idProductProcess']]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM products_process WHERE id_product_process = :id_product_process");
            $stmt->execute(['id_product_process' => $dataProductProcess['idProductProcess']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
