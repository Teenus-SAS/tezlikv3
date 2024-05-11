<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class IndirectCostDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Buscar producto por el idMachine
    public function findProductByMachine($idMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT id_product, employee
                                      FROM products_process
                                      WHERE id_machine = :id_machine AND id_company = :id_company");
            $stmt->execute([
                'id_machine' => $idMachine,
                'id_company' => $id_company
            ]);
            $dataProduct = $stmt->fetchAll($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dataProduct = array('info' => true, 'message' => $message);
        }

        return $dataProduct;
    }

    // Buscar la maquina asociada al producto
    public function findMachineByProduct($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT pp.id_product_process, pp.id_machine, m.minute_depreciation, pp.enlistment_time, pp.operation_time, pp.efficiency
                                      FROM products_process pp 
                                      INNER JOIN machines m ON m.id_machine = pp.id_machine 
                                      WHERE pp.id_product = :id_product AND pp.id_company = :id_company");
            $stmt->execute([
                'id_product' => $idProduct,
                'id_company' => $id_company
            ]);
            $dataProductMachine = $stmt->fetchAll($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dataProductMachine = array('info' => true, 'message' => $message);
        }
        return $dataProductMachine;
    }


    // Suma el costo por minuto de la carga fabril y calcular costo indirecto
    public function calcIndirectCost($dataProductMachine)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $indirectCost = 0;

            for ($i = 0; $i < sizeof($dataProductMachine); $i++) {

                // Suma el costo por minuto de la carga fabril
                $stmt = $connection->prepare("SELECT IFNULL(SUM(cost_minute), 0) AS totalCostMinute
                                          FROM manufacturing_load
                                          WHERE id_machine = :id_machine");
                $stmt->execute(['id_machine' => $dataProductMachine[$i]['id_machine']]);
                $dataCostManufacturingLoad = $stmt->fetch($connection::FETCH_ASSOC);

                // Calculo costo indirecto
                // $processMachineindirectCost = ($dataCostManufacturingLoad['totalCostMinute'] + $dataProductMachine[$i]['minute_depreciation']) * $dataProductMachine[$i]['operation_time'];
                $processMachineindirectCost = 0;

                $factoryAMachine = $dataCostManufacturingLoad['totalCostMinute'] + $dataProductMachine[$i]['minute_depreciation'];

                $dataProductMachine[$i]['efficiency'] == 0 ? $efficiency = 100 : $efficiency = $dataProductMachine[$i]['efficiency'];

                $totalTime = ($dataProductMachine[$i]['enlistment_time'] + $dataProductMachine[$i]['operation_time']) / ($efficiency / 100);
                $processMachineindirectCost = $factoryAMachine * $totalTime;
                // Guardar Costo indirecto
                $this->updateCostIndirectCost($processMachineindirectCost, $dataProductMachine[$i]['id_product_process']);

                $indirectCost = $indirectCost + $processMachineindirectCost;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $indirectCost = array('info' => true, 'message' => $message);
        }
        return $indirectCost;
    }

    // Modificar costo indirecto de products_process
    public function updateCostIndirectCost($indirectCost, $idProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_process SET indirect_cost = :indirect_cost WHERE id_product_process = :id_product_process");
            $stmt->execute([
                'indirect_cost' => $indirectCost,
                'id_product_process' => $idProductProcess,
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateCostIndirectCostByProduct($indirectCost, $idProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_process SET indirect_cost = :indirect_cost WHERE id_product = :id_product");
            $stmt->execute([
                'indirect_cost' => $indirectCost,
                'id_product' => $idProduct,
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // Modificar total costo indirecto de products_costs
    public function updateTotalCostIndirectCost($indirectCost, $idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_costs SET cost_indirect_cost = :cost_indirect_cost
                                      WHERE id_product = :id_product AND id_company = :id_company");
            $stmt->execute([
                'cost_indirect_cost' => $indirectCost,
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
