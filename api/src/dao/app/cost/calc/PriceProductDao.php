<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PriceProductDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Calcular precio del producto
    public function findTotalPrice($idProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT
                                        ((pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost + ed.assignable_expense +  IF(SUM(cost) IS NULL, 0, SUM(s.cost)))
                                        /((100-pc.commission_sale-pc.profitability)/100)) as totalPrice 
                                      FROM products_costs pc
                                      LEFT JOIN services s ON s.id_product = pc.id_product
                                      LEFT JOIN expenses_distribution ed ON ed.id_product = pc.id_product
                                      WHERE pc.id_product = :id_product");
        $stmt->execute(['id_product' => $idProduct]);
        $dataPrice = $stmt->fetch($connection::FETCH_ASSOC);

        return $dataPrice;
    }

    // Modificar precio
    public function updatePrice($idProduct, $totalPrice)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("UPDATE products_costs SET price = :price WHERE id_product = :id_product");
        $stmt->execute([
            'price' => $totalPrice,
            'id_product' => $idProduct
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }

    // Calcular precio General
    public function calcPrice($idProduct)
    {
        $dataPrice = $this->findTotalPrice($idProduct);
        $this->updatePrice($idProduct, $dataPrice['totalPrice']);
    }

    // Calcular precio por Maquina
    public function calcPriceByMachine($idMachine, $id_company)
    {
        $machine = new IndirectCostDao();
        $dataProduct = $machine->findProductByMachine($idMachine, $id_company);

        for ($i = 0; $i < sizeof($dataProduct); $i++) {
            $dataPrice = $this->findTotalPrice($dataProduct[$i]['id_product']);

            if ($dataPrice) {
                // No existe el producto asociado a la tabla products_cost";
                $this->updatePrice($dataProduct[$i]['id_product'], $dataPrice['totalPrice']);
            }
        }
    }

    // Calcular precio por Materia Prima
    public function calcPriceByMaterial($idMaterial, $id_company)
    {
        $material = new CostMaterialsDao();
        $dataProduct = $material->findProductByMaterial($idMaterial, $id_company);

        if (!empty($dataProduct)) {
            // No hay ningun producto asociado a esa materia prima
            for ($i = 0; $i < sizeof($dataProduct); $i++) {
                $dataPrice = $this->findTotalPrice($dataProduct[$i]['id_product']);
                $this->updatePrice($dataProduct[$i]['id_product'], $dataPrice['totalPrice']);
            }
        }
    }

    // Calcular precio por Nomina
    public function calcPriceByPayroll($idProcess, $id_company)
    {
        $process = new CostWorkforceDao();
        $dataProduct = $process->findProductByProcess($idProcess, $id_company);

        for ($i = 0; $i < sizeof($dataProduct); $i++) {
            // Calcular precio del producto
            $dataPrice = $this->findTotalPrice($dataProduct[$i]['id_product']);

            if ($dataPrice) {
                // No existe el producto asociado a la tabla products_cost";
                $this->updatePrice($dataProduct[$i]['id_product'], $dataPrice['totalPrice']);
            }
        }
        //$this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
}
