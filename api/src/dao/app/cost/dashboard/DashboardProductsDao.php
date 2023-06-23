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
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pc.cost_materials, pc.cost_workforce, IF(p.id_family = 0, IFNULL(ed.assignable_expense, 0), f.assignable_expense) AS assignable_expense, IFNULL(er.expense_recover, 0) AS expense_recover, pc.cost_indirect_cost, 
                                             pc.profitability, IF(p.id_family != 0, (SELECT IFNULL(SUM(units_sold), 0) FROM families WHERE id_company = p.id_company), (SELECT IFNULL(SUM(units_sold), 0) FROM expenses_distribution WHERE id_company = p.id_company)) AS units_sold,
                                             IF(p.id_family != 0, (SELECT IFNULL(SUM(turnover), 0) FROM families WHERE id_company = p.id_company), (SELECT IFNULL(SUM(turnover),0) FROM expenses_distribution WHERE id_company = p.id_company)) AS turnover, IFNULL((SELECT SUM(cost) FROM services WHERE id_product = p.id_product), 0) AS services, pc.commission_sale, pc.price, p.img
                                        FROM products_costs pc
                                            INNER JOIN products p ON p.id_product = pc.id_product
                                            LEFT JOIN expenses_distribution ed ON ed.id_product = pc.id_product
                                            LEFT JOIN expenses_recover er ON er.id_product = pc.id_product
                                            LEFT JOIN families f ON f.id_family = p.id_family
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
        $stmt = $connection->prepare("SELECT pp.id_product_process, pp.id_product, pp.id_machine, pc.id_process, pc.process, (pp.enlistment_time + pp.operation_time) AS totalTime, pp.enlistment_time, pp.operation_time,
                                             IFNULL(m.machine, 'PROCESO MANUAL') AS machine, IFNULL(m.cost, 0) AS cost_machine, IFNULL(m.years_depreciation, 0) AS years_depreciation, IFNULL(m.residual_value, 0) AS residual_value, IFNULL(m.minute_depreciation, 0) AS minute_depreciation, IFNULL(m.hours_machine, 0) AS hours_machine, IFNULL(m.days_machine, 0) AS days_machine
                                      FROM products_process pp 
                                        INNER JOIN process pc ON pc.id_process = pp.id_process
                                        LEFT JOIN machines m ON m.id_machine = pp.id_machine
                                      WHERE pp.id_product = :id_product AND pp.id_company = :id_company");
        $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $totalTimeProcess = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("totalTime", array('totalTime' => $totalTimeProcess));
        return $totalTimeProcess;
    }

    public function findAverageTimeProcessByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT IFNULL(SUM(pp.enlistment_time), 0) AS enlistment_time, IFNULL(SUM(pp.operation_time), 0) AS operation_time
                                      FROM products p
                                        LEFT JOIN products_process pp ON pp.id_product = p.id_product
                                      WHERE p.id_product = :id_product AND p.id_company = :id_company
                                      ORDER BY `p`.`product` ASC");
        $stmt->execute([
            'id_product' => $id_product,
            'id_company' => $id_company
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $averageTimeProcess = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->notice("averageTimeProcess", array('averageTimeProcess' => $averageTimeProcess));
        return $averageTimeProcess;
    }

    public function findCostWorkforceByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.process, IFNULL((SELECT SUM((pr.enlistment_time + pr.operation_time) * py.minute_value) FROM payroll py INNER JOIN products_process pr ON pr.id_process = py.id_process 
                                             WHERE pr.id_product = pp.id_product AND pr.id_process = p.id_process), 0) AS workforce	
                                      FROM process p
                                      INNER JOIN products_process pp ON pp.id_process = p.id_process
                                      WHERE pp.id_product = :id_product AND pp.id_company = :id_company");
        $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $costWorkforce = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("costWorkforce", array('costWorkforce' => $costWorkforce));
        return $costWorkforce;
    }

    public function findCostRawMaterialsByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pm.id_product_material, pm.id_product, pm.id_material, m.reference, m.material, pm.cost AS totalCostMaterial, m.cost AS cost_material, pm.id_unit AS unit_product_material, m.unit AS unit_material, cm.magnitude, cu.abbreviation AS abbreviation_material, (SELECT ccu.abbreviation FROM products_materials cpm
                                                INNER JOIN convert_units ccu ON ccu.id_unit = cpm.id_unit WHERE cpm.id_product_material = pm.id_product_material) AS abbreviation_p_materials, pm.quantity, pm.cost AS cost_product_materials
                                        FROM products_materials pm
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN convert_units cu ON cu.id_unit = m.unit
                                        INNER JOIN convert_magnitudes cm ON cm.id_magnitude = cu.id_magnitude
                                      WHERE pm.id_product = :id_product AND pm.id_company = :id_company 
                                      ORDER BY totalCostMaterial DESC");
        $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $costRawMaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("costRawMaterials", array('costRawMaterials' => $costRawMaterials));
        return $costRawMaterials;
    }
}
