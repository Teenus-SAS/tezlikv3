<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class HistoricalProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findResumeHistorical(int $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT month, year, COUNT(*) AS total_productos, MAX(date_register) AS ultima_fecha_registro
                FROM tezlikso_HistProduccion.historical_products
                WHERE id_company = :id_company AND deleted_at IS NULL
                GROUP BY month, year
                ORDER BY year DESC, month DESC;";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findAllHistoricalByCompany($id_company, $year, $period)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT p.id_product, p.reference, p.product, hp.id_historic, hp.month, hp.year, hp.price, hp.sale_price, hp.profitability, hp.min_profitability, pc.cost_workforce AS actual_cost_workforce, pc.cost_materials AS actual_cost_materials, pc.cost_indirect_cost AS actual_cost_indirect_cost, pc.profitability AS actual_profitability, pc.commission_sale AS actual_commission_sale, pc.sale_price AS actual_sale_price, pc.price AS actual_price, 
                IF(cl.flag_family = 2, 
                    (SELECT IFNULL(SUM(units_sold), 0) FROM tezlikso_tezlikProduccion.families 
                    WHERE id_company = p.id_company), 
                    (SELECT IFNULL(SUM(units_sold), 0) FROM tezlikso_tezlikProduccion.expenses_distribution
                    WHERE id_company = p.id_company)) AS actual_units_sold, 
                IF(cl.flag_family = 2, 
                    (SELECT IFNULL(SUM(turnover), 0) FROM tezlikso_tezlikProduccion.families 
                    WHERE id_company = p.id_company), 
                    (SELECT IFNULL(SUM(turnover), 0) FROM tezlikso_tezlikProduccion.expenses_distribution 
                    WHERE id_company = p.id_company)) AS actual_turnover, 
                IF(cl.flag_family = 2, 
                    IFNULL(f.assignable_expense, 0), 
                    IFNULL(ed.assignable_expense, 0)) AS actual_assignable_expense, 
                    IFNULL(er.expense_recover, 0) AS expense_recover, 
                    IFNULL((SELECT SUM(cost) FROM tezlikso_tezlikProduccion.services 
                    WHERE id_product = p.id_product), 0) AS actual_services FROM tezlikso_tezlikProduccion.products p
                JOIN tezlikso_HistProduccion.historical_products hp ON hp.id_product = p.id_product
                LEFT JOIN tezlikso_tezlikProduccion.products_costs pc ON pc.id_product = p.id_product
                LEFT JOIN tezlikso_tezlikProduccion.companies_licenses cl ON cl.id_company = p.id_company
                LEFT JOIN tezlikso_tezlikProduccion.expenses_distribution ed ON ed.id_product = p.id_product
                LEFT JOIN tezlikso_tezlikProduccion.expenses_recover er ON er.id_product = p.id_product
                LEFT JOIN tezlikso_tezlikProduccion.families f ON f.id_family = p.id_family
                WHERE p.id_company = :id_company AND month = :period AND year = :year  ";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company, 'period' => $period, 'year' => $year]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findHistorical($id_historic)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT p.id_product, p.reference, p.product, p.img, hp.id_historic, hp.month, hp.year, hp.price, hp.sale_price, hp.profitability, hp.min_profitability, hp.commision_sale, hp.commision_sale AS commission_sale, hp.expense_recover,
                        hp.cost_material, hp.cost_material AS cost_materials, hp.cost_workforce, hp.cost_indirect, hp.cost_indirect AS cost_indirect_cost, hp.external_services, hp.external_services AS services, hp.units_sold, hp.turnover, hp.assignable_expense
                FROM tezlikso_tezlikProduccion.products p
                JOIN tezlikso_HistProduccion.historical_products hp ON hp.id_product = p.id_product
                WHERE hp.id_historic = :id_historic";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'id_historic' => $id_historic
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetch($connection::FETCH_ASSOC);
        return $products;
    }

    // public function findHistorical($id_product)
    // {
    //     $connection = Connection::getInstance()->getConnection1();

    //     $stmt = $connection->prepare("SELECT * FROM historical_products WHERE id_product = :id_product");
    //     $stmt->execute(['id_product' => $id_product]);
    //     $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    //     $products = $stmt->fetch($connection::FETCH_ASSOC);
    //     return $products;
    // }

    public function findLastHistorical($id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection1();

            $sql = "SELECT hp.date_product
                    FROM products p
                    JOIN tezlikso_HistProduccion.historical_products hp ON hp.id_product = p.id_product 
                    WHERE p.id_company = :id_company ORDER BY hp.date_product ASC LIMIT 1";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_company' => $id_company]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

            $products = $stmt->fetch($connection::FETCH_ASSOC);
            return $products;
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function deleteSoftPeriodHistorical(int $id_company, int $id_user, array $data, $connection = null)
    {
        $useExternalConnection = $connection !== null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "UPDATE tezlikso_HistProduccion.historical_products SET deleted_at = :deleted_at, deleted_by = :deleted_by 
                    WHERE id_company = :id_company AND year = :year AND month = :month AND deleted_at IS NULL";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_company' => $id_company,
                'year' => $data['year'],
                'month' => $data['month'],
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => $id_user,
            ]);
        } catch (\PDOException $e) {
            $this->logger->critical(__FUNCTION__ . ': Error de base de datos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => true, 'message' => 'Error al actualizar los datos históricos'];
            if (!$useExternalConnection && isset($connection)) {
                $connection->rollBack();
            }
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__ . ': Error general', [
                'error' => $e->getMessage()
            ]);
            return ['error' => true, 'message' => $e->getMessage()];
        } finally {
            if (!$useExternalConnection && isset($connection)) {
                $connection = null;
            }
        }
    }

    public function insertHistoricalByCompany($dataHistorical, $id_company, $connection = null)
    {
        $useExternalConnection = $connection !== null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "INSERT INTO tezlikso_HistProduccion.historical_products 
                        (month, year, id_company, id_product, price, sale_price, profitability, min_profitability, commision_sale, 
                        cost_material, cost_workforce, cost_indirect, external_services, units_sold, turnover, assignable_expense, 
                        expense_recover)
                    VALUES 
                        (:month, :year, :id_company, :id_product, :price, :sale_price, :profitability, :min_profitability, :commision_sale, 
                        :cost_material, :cost_workforce, :cost_indirect, :external_services, :units_sold, :turnover, :assignable_expense, 
                        :expense_recover)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'month' => $dataHistorical['month'],
                'year' => $dataHistorical['year'],
                'id_company' => $id_company,
                'id_product' => $dataHistorical['idProduct'],
                'price' => $dataHistorical['price'],
                'sale_price' => $dataHistorical['salePrice'],
                'profitability' => $dataHistorical['profitability'],
                'min_profitability' => $dataHistorical['minProfitability'],
                'commision_sale' => $dataHistorical['commisionSale'],
                'cost_material' => $dataHistorical['costMaterials'],
                'cost_workforce' => $dataHistorical['costWorkforce'],
                'cost_indirect' => $dataHistorical['costIndirect'],
                'external_services' => $dataHistorical['externalServices'],
                'units_sold' => $dataHistorical['unitsSold'],
                'turnover' => $dataHistorical['turnover'],
                'assignable_expense' => $dataHistorical['assignableExpense'],
                'expense_recover' => $dataHistorical['expenseRecover']
            ]);
        } catch (\PDOException $e) {
            $this->logger->critical(__FUNCTION__ . ': Error de base de datos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => true, 'message' => 'Error al insertar los datos históricos'];
            if (!$useExternalConnection && isset($connection)) {
                $connection->rollBack();
            }
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__ . ': Error general', [
                'error' => $e->getMessage()
            ]);
            return ['error' => true, 'message' => $e->getMessage()];
        } finally {
            if (!$useExternalConnection && isset($connection)) {
                $connection = null;
            }
        }
    }

    public function updateHistoricalByCompany($dataHistorical, $connection = null)
    {
        $useExternalConnection = $connection !== null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {
            $sql = "UPDATE tezlikso_HistProduccion.historical_products 
                    SET price = :price, sale_price = :sale_price, profitability = :profitability, min_profitability = :min_profitability, commision_sale = :commision_sale, cost_material = :cost_material, cost_workforce = :cost_workforce, 
                        cost_indirect = :cost_indirect, external_services = :external_services, units_sold = :units_sold, turnover = :turnover, assignable_expense = :assignable_expense, expense_recover = :expense_recover
                    WHERE id_product = :id_product AND month = :month AND year = :year";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_product' => $dataHistorical['idProduct'],
                'month' => $dataHistorical['month'],
                'year' => $dataHistorical['year'],
                'price' => $dataHistorical['price'],
                'sale_price' => $dataHistorical['salePrice'],
                'profitability' => $dataHistorical['profitability'],
                'min_profitability' => $dataHistorical['minProfitability'],
                'commision_sale' => $dataHistorical['commisionSale'],
                'cost_material' => $dataHistorical['costMaterials'],
                'cost_workforce' => $dataHistorical['costWorkforce'],
                'cost_indirect' => $dataHistorical['costIndirect'],
                'external_services' => $dataHistorical['externalServices'],
                'units_sold' => $dataHistorical['unitsSold'],
                'turnover' => $dataHistorical['turnover'],
                'assignable_expense' => $dataHistorical['assignableExpense'],
                'expense_recover' => $dataHistorical['expenseRecover']
            ]);
        } catch (\PDOException $e) {
            $this->logger->critical(__FUNCTION__ . ': Error de base de datos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => true, 'message' => 'Error al insertar los datos históricos'];
            if (!$useExternalConnection && isset($connection)) {
                $connection->rollBack();
            }
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__ . ': Error general', [
                'error' => $e->getMessage()
            ]);
            return ['error' => true, 'message' => $e->getMessage()];
        } finally {
            if (!$useExternalConnection && isset($connection)) {
                $connection = null;
            }
        }
    }
}
