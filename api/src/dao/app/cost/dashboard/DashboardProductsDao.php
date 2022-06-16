<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DashboardProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Costos y Gastos productos
    public function findCostAnalysisByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.product, pc.cost_materials, pc.cost_workforce, ed.assignable_expense, pc.cost_indirect_cost, 
                                             pc.profitability, ed.units_sold, ed.turnover, pc.commission_sale, pc.price
                                      FROM products_costs pc
                                      INNER JOIN products p ON p.id_product = pc.id_product
                                      INNER JOIN expenses_distribution ed ON ed.id_product = pc.id_product
                                      WHERE pc.id_product = :id_product AND pc.id_company = :id_company");
        $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $costAnalysisProducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("cost", array('cost' => $costAnalysisProducts));
        return $costAnalysisProducts;
    }

    public function findProductProcessByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pc.process, (pp.enlistment_time + pp.operation_time) AS totalTime
                                      FROM products_process pp 
                                      INNER JOIN process pc ON pc.id_process = pp.id_process
                                      WHERE pp.id_product = :id_product AND pp.id_company = :id_company");
        $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $totalTimeProcess = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("totalTime", array('totalTime' => $totalTimeProcess));
        return $totalTimeProcess;
    }

    public function findCostWorkforceByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.process, ((pp.enlistment_time + pp.operation_time) * py.minute_value) AS workforce
                                      FROM payroll py
                                      INNER JOIN process p ON p.id_process = py.id_process
                                      INNER JOIN products_process pp ON pp.id_process = py.id_process
                                      WHERE pp.id_product = :id_product AND py.id_company = :id_company");
        $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $costWorkforce = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("costWorkforce", array('costWorkforce' => $costWorkforce));
        return $costWorkforce;
    }

    public function findCostRawMaterialsByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT m.material, (pm.quantity * m.cost) AS totalCostMaterial
                                      FROM products_materials pm
                                      INNER JOIN materials m ON m.id_material = pm.id_material
                                      WHERE pm.id_product = :id_product AND pm.id_company = :id_company 
                                      ORDER BY totalCostMaterial DESC");
        $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $costRawMaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("costRawMaterials", array('costRawMaterials' => $costRawMaterials));
        return $costRawMaterials;
    }
}
