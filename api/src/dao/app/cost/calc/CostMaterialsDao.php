<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CostMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Buscar producto por el idMaterial
    public function findProductByMaterial($idMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_product FROM products_materials 
                                      WHERE id_material =:id_material AND id_company = :id_company");
        $stmt->execute(['id_material' => $idMaterial, 'id_company' => $id_company]);
        $dataProduct = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $dataProduct;
    }

    // Buscar costo total de la materia prima y modificar en products_costs
    public function findTotalCostAndModify($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT SUM(pm.quantity * m.cost) as cost 
                                        FROM products_materials pm 
                                        INNER JOIN materials m ON pm.id_material = m.id_material 
                                        WHERE pm.id_company = :id_company AND pm.id_product = :id_product");
        $stmt->execute(['id_company' => $id_company, 'id_product' => $idProduct]);
        $costMaterialsProduct = $stmt->fetch($connection::FETCH_ASSOC);

        $stmt = $connection->prepare("UPDATE products_costs SET cost_materials = :materials
                                         WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'materials' => $costMaterialsProduct['cost'],
            'id_product' => $idProduct,
            'id_company' => $id_company
        ]);
    }

    // General
    public function calcCostMaterial($idProduct, $id_company)
    {
        $this->findTotalCostAndModify($idProduct, $id_company);
    }

    /* Al modificar materia prima */
    public function calcCostMaterialsByRawMaterial($dataMaterials, $id_company)
    {
        $dataProduct = $this->findProductByMaterial($dataMaterials['idMaterial'], $id_company);

        for ($i = 0; $i < sizeof($dataProduct); $i++) {
            $this->findTotalCostAndModify($dataProduct[$i]['id_product'], $id_company);
        }

        //$this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
}
