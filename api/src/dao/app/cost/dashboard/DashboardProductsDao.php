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
        $stmt = $connection->prepare("SELECT 
                                            -- Informacion Basica Producto
                                                p.id_product, 
                                                p.id_family, 
                                                p.reference, 
                                                p.product, 
                                                p.img,
                                                p.composite,
                                            -- Costeo total
                                                pc.cost_materials, 
                                                pc.cost_indirect_cost, 
                                                pc.cost_workforce, 
                                                IFNULL((SELECT SUM(cost) FROM services WHERE id_product = p.id_product), 0) AS services, 
                                                pc.profitability, 
                                                pc.commission_sale, 
                                            -- Precios Producto
                                                pc.price, 
                                                pc.sale_price, 
                                                pc.price_usd, 
                                                pc.sale_price_usd, 
                                                pc.price_eur, 
                                                pc.sale_price_eur,
                                            -- Ventas
                                                IF(cl.flag_family = 2, IFNULL(f.assignable_expense, 0), IFNULL(ed.assignable_expense, 0)) AS assignable_expense, 
                                                IFNULL(er.expense_recover, 0) AS expense_recover, 
                                                IFNULL(IF(cl.flag_family = 2, (SELECT units_sold FROM families WHERE id_family = p.id_family), (SELECT units_sold FROM expenses_distribution WHERE id_product = p.id_product)), 0) AS units_sold,
                                                IFNULL(IF(cl.flag_family = 2, (SELECT turnover FROM families WHERE id_family = p.id_family), (SELECT turnover FROM expenses_distribution WHERE id_product = p.id_product)), 0) AS turnover       
                                        FROM products_costs pc
                                            INNER JOIN products p ON p.id_product = pc.id_product
                                            LEFT JOIN expenses_distribution ed ON ed.id_product = pc.id_product
                                            LEFT JOIN expenses_recover er ON er.id_product = pc.id_product
                                            LEFT JOIN families f ON f.id_family = p.id_family
                                            INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                                        WHERE pc.id_product = :id_product AND pc.id_company = :id_company");
        $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);



        $costAnalysisProducts = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $costAnalysisProducts;
    }

    public function findProductProcessByProduct($id_product, $id_company, $op)
    {
        $connection = Connection::getInstance()->getConnection();
        if ($op == 1) {
            $stmt = $connection->prepare("SELECT pp.id_product_process, pp.id_product, pp.id_machine, pc.id_process, pc.process, (pp.enlistment_time + pp.operation_time) AS totalTime, pp.enlistment_time, pp.operation_time, pp.efficiency,
                                             IFNULL(m.machine, 'PROCESO MANUAL') AS machine, IFNULL(m.cost, 0) AS cost_machine, IFNULL(m.years_depreciation, 0) AS years_depreciation, IFNULL(m.residual_value, 0) AS residual_value, IFNULL(m.minute_depreciation, 0) AS minute_depreciation, IFNULL(m.hours_machine, 0) AS hours_machine, IFNULL(m.days_machine, 0) AS days_machine
                                      FROM products_process pp 
                                        INNER JOIN process pc ON pc.id_process = pp.id_process
                                        LEFT JOIN machines m ON m.id_machine = pp.id_machine
                                      WHERE pp.id_product = :id_product AND pp.id_company = :id_company");
            $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);
        } else {
            $stmt = $connection->prepare("SELECT pp.id_product_process, pp.id_product, pp.id_machine, pc.id_process, pc.process, (pp.enlistment_time + pp.operation_time) AS totalTime, pp.enlistment_time, pp.operation_time, pp.efficiency,
                                         IFNULL(m.machine, 'PROCESO MANUAL') AS machine, IFNULL(m.cost, 0) AS cost_machine, IFNULL(m.years_depreciation, 0) AS years_depreciation, IFNULL(m.residual_value, 0) AS residual_value, IFNULL(m.minute_depreciation, 0) AS minute_depreciation, IFNULL(m.hours_machine, 0) AS hours_machine, IFNULL(m.days_machine, 0) AS days_machine
                                  FROM products_process pp 
                                    INNER JOIN process pc ON pc.id_process = pp.id_process
                                    LEFT JOIN machines m ON m.id_machine = pp.id_machine
                                  WHERE pp.id_product IN ($id_product) AND pp.id_company = :id_company");
            $stmt->execute(['id_company' => $id_company]);
        }



        $totalTimeProcess = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $totalTimeProcess;
    }

    public function findAverageTimeProcessByProduct($id_product, $id_company, $op)
    {
        $connection = Connection::getInstance()->getConnection();

        if ($op == 1) {
            $stmt = $connection->prepare("SELECT IFNULL(SUM(pp.enlistment_time), 0) AS enlistment_time, IFNULL(SUM(pp.operation_time), 0) AS operation_time
                                      FROM products p
                                        LEFT JOIN products_process pp ON pp.id_product = p.id_product
                                      WHERE p.id_product = :id_product AND p.id_company = :id_company
                                      ORDER BY `p`.`product` ASC");
            $stmt->execute([
                'id_product' => $id_product,
                'id_company' => $id_company
            ]);
        } else {
            $stmt = $connection->prepare("SELECT IFNULL(SUM(pp.enlistment_time), 0) AS enlistment_time, IFNULL(SUM(pp.operation_time), 0) AS operation_time
                                      FROM products p
                                        LEFT JOIN products_process pp ON pp.id_product = p.id_product
                                      WHERE p.id_product IN ($id_product) AND p.id_company = :id_company
                                      ORDER BY `p`.`product` ASC");
            $stmt->execute([
                'id_company' => $id_company
            ]);
        }



        $averageTimeProcess = $stmt->fetch($connection::FETCH_ASSOC);


        return $averageTimeProcess;
    }

    public function findCostWorkforceByProduct($id_product, $id_company, $op)
    {
        $connection = Connection::getInstance()->getConnection();
        if ($op == 1) {
            $stmt = $connection->prepare("SELECT p.process, IFNULL((SELECT IFNULL(SUM(IFNULL(py.minute_value, 0) *(IFNULL((IFNULL(pr.enlistment_time, 0) + IFNULL(pr.operation_time, 0)) / IFNULL((IF(pr.efficiency = 0, 100, pr.efficiency) / 100), 0), 0))), 0)
                                             FROM payroll py INNER JOIN products_process pr ON pr.id_process = py.id_process AND pr.auto_machine = 0
                                             WHERE pr.id_product = pp.id_product AND pr.id_process = p.id_process), 0) AS workforce	
                                      FROM process p
                                      INNER JOIN products_process pp ON pp.id_process = p.id_process
                                      WHERE pp.id_product = :id_product AND pp.id_company = :id_company");
            $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);
        } else {
            $stmt = $connection->prepare("SELECT p.process, IFNULL((SELECT IFNULL(SUM(IFNULL(py.minute_value, 0) *(IFNULL((IFNULL(pr.enlistment_time, 0) + IFNULL(pr.operation_time, 0)) / IFNULL((IF(pr.efficiency = 0, 100, pr.efficiency) / 100), 0), 0))), 0)
                                             FROM payroll py INNER JOIN products_process pr ON pr.id_process = py.id_process AND pr.auto_machine = 0
                                             WHERE pr.id_product = pp.id_product AND pr.id_process = p.id_process), 0) AS workforce	
                                      FROM process p
                                      INNER JOIN products_process pp ON pp.id_process = p.id_process
                                      WHERE pp.id_product IN ($id_product) AND pp.id_company = :id_company");
            $stmt->execute(['id_company' => $id_company]);
        }



        $costWorkforce = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $costWorkforce;
    }

    public function findCostRawMaterialsByProduct($id_product, $id_company, $op)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pm.id_product_material, pm.id_product, pm.id_material, m.reference, m.material, pm.cost AS totalCostMaterial, m.cost AS cost_material, pm.id_unit AS unit_product_material, m.unit AS unit_material, cm.magnitude, cu.abbreviation AS abbreviation_material, (SELECT ccu.abbreviation FROM products_materials cpm
                                            INNER JOIN convert_units ccu ON ccu.id_unit = cpm.id_unit WHERE cpm.id_product_material = pm.id_product_material) AS abbreviation_p_materials, pm.quantity, pm.cost AS cost_product_materials, pm.waste, ((pm.cost / pc.cost_materials)* 100) AS participation
                                    FROM products_materials pm
                                    INNER JOIN products_costs pc ON pc.id_product = pm.id_product
                                    INNER JOIN materials m ON m.id_material = pm.id_material
                                    INNER JOIN convert_units cu ON cu.id_unit = m.unit
                                    INNER JOIN convert_magnitudes cm ON cm.id_magnitude = cu.id_magnitude
                                  WHERE pm.id_product = :id_product AND pm.id_company = :id_company 
                                  ORDER BY participation DESC");
        $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);

        $costRawMaterials = $stmt->fetchAll($connection::FETCH_ASSOC);

        if ($op == 2) {
            // $stmt = $connection->prepare("SELECT p.product AS material, cp.cost AS totalCostMaterial
            //                             FROM composite_products cp
            //                             LEFT JOIN products p ON p.id_product = cp.id_child_product 
            //                           WHERE cp.id_product = :id_product AND cp.id_company = :id_company 
            //                           GROUP BY p.id_product 
            //                           ORDER BY totalCostMaterial DESC");
            $stmt = $connection->prepare("SELECT p.product AS material, cp.cost AS totalCostMaterial, ((cp.cost / pc.cost_materials) * 100) AS participation
                                        FROM composite_products cp
                                        LEFT JOIN products p ON p.id_product = cp.id_child_product
                                        INNER JOIN products_costs pc ON pc.id_product = cp.id_child_product
                                      WHERE cp.id_product = :id_product AND cp.id_company = :id_company 
                                      GROUP BY p.id_product 
                                      ORDER BY participation DESC");
            $stmt->execute(['id_product' => $id_product, 'id_company' => $id_company]);

            $data = $stmt->fetchAll($connection::FETCH_ASSOC);

            $costRawMaterials = array_merge($costRawMaterials, $data);

            foreach ($costRawMaterials as $key => $row) {
                $cost[$key] = $row['participation'];
            }

            array_multisort($cost, SORT_DESC, $costRawMaterials);
        }


        return $costRawMaterials;
    }
}
