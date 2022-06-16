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

        $stmt = $connection->prepare("SELECT id_product 
                                      FROM products_process
                                      WHERE id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute(['id_machine' => $idMachine, 'id_company' => $id_company]);
        $dataProduct = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $dataProduct;
    }

    // Buscar la maquina asociada al producto
    public function findMachineByProduct($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pp.id_machine, m.minute_depreciation, (pp.enlistment_time + pp.operation_time) AS totalTime 
                                      FROM products_process pp 
                                      INNER JOIN machines m ON m.id_machine = pp.id_machine 
                                      WHERE pp.id_product = :id_product AND pp.id_company = :id_company ");
        $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
        $dataProductMachine = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $dataProductMachine;
    }

    // Suma el costo por minuto de la carga fabril y calcular costo indirecto
    public function calcCostMinuteAndIndirectCost($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        // Buscar la maquina asociada al producto
        $dataProductMachine = $this->findMachineByProduct($idProduct, $id_company);

        $indirectCost = 0;

        for ($i = 0; $i < sizeof($dataProductMachine); $i++) {

            // Suma el costo por minuto de la carga fabril
            $stmt = $connection->prepare("SELECT SUM(cost_minute) AS totalCostMinute
                                          FROM manufacturing_load
                                          WHERE id_machine = :id_machine");
            $stmt->execute(['id_machine' => $dataProductMachine[$i]['id_machine']]);
            $dataCostManufacturingLoad = $stmt->fetch($connection::FETCH_ASSOC);

            // Calculo costo indirecto
            $processMachineindirectCost = ($dataCostManufacturingLoad['totalCostMinute'] + $dataProductMachine[$i]['minute_depreciation']) * $dataProductMachine[$i]['totalTime'];

            $indirectCost = $indirectCost + $processMachineindirectCost;
        }
        return $indirectCost;
    }

    // Busqueda del producto asociado con la maquina 
    public function findProductAndIndirectCostToModify($idMachine, $id_company)
    {
        // Buscar prducto por idMachine
        $dataProduct = $this->findProductByMachine($idMachine, $id_company);

        for ($i = 0; $i < sizeof($dataProduct); $i++) {
            // Buscar la maquina asociada al producto
            $this->findMachineByProduct($dataProduct[$i]['id_product'], $id_company);

            // Suma el costo por minuto de la carga fabril y calcular costo indirecto
            $indirectCost = $this->calcCostMinuteAndIndirectCost($dataProduct[$i]['id_product'], $id_company);

            // Modificar costo indirecto del producto 
            $this->updateCostIndirectCost($indirectCost, $dataProduct[$i]['id_product'], $id_company);
        }
    }

    // Modificar costo indirecto de products_costs
    public function updateCostIndirectCost($indirectCost, $idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("UPDATE products_costs SET cost_indirect_cost = :cost_indirect_cost
                                      WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'cost_indirect_cost' => $indirectCost,
            'id_product' => $idProduct,
            'id_company' => $id_company
        ]);
    }

    // General
    public function calcCostIndirectCost($dataProductProcess, $id_company)
    {
        $this->findMachineByProduct($dataProductProcess['idProduct'], $id_company);

        $indirectCost = $this->calcCostMinuteAndIndirectCost($dataProductProcess['idProduct'], $id_company);

        $this->updateCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
    }

    /* Al modificar la maquina */
    public function calcCostIndirectCostByMachine($dataMachine, $id_company)
    {
        $this->findProductAndIndirectCostToModify($dataMachine['idMachine'], $id_company);
    }

    /* Al modificar la carga fabril */
    public function calcCostIndirectCostByFactoryLoad($dataFactoryLoad, $id_company)
    {
        $this->findProductAndIndirectCostToModify($dataFactoryLoad['idMachine'], $id_company);
    }
}
