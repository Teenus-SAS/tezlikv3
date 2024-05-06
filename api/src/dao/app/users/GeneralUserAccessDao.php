<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralUserAccessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findUserAccessByUser($id_user)
    {
        $connection = Connection::getInstance()->getConnection();
        // IFNULL(cua.price_usd, 0) AS price_usd,
        $stmt = $connection->prepare("SELECT 
                                        -- Accesos usuario
                                            IFNULL(cua.create_product, 0) AS cost_product, 
                                            IFNULL(cua.create_materials, 0) AS cost_material, 
                                            IFNULL(cua.create_machines, 0) AS cost_machine, 
                                            IFNULL(cua.create_process, 0) AS cost_process, 
                                            IFNULL(cua.product_materials, 0) AS cost_products_material, 
                                            IFNULL(cua.factory_load, 0) AS factory_load,
                                            IFNULL(cua.contract, 0) AS contract, 
                                            IFNULL(cua.payroll_load, 0) AS payroll_load, 
                                            IFNULL(cua.type_payroll, 0) AS type_payroll, 
                                            IFNULL(cua.expense, 0) AS expense, 
                                            IFNULL(cua.expense_distribution, 0) AS expense_distribution, 
                                            IFNULL(cua.production_center, 0) AS production_center, 
                                            IFNULL(cua.type_expense, 0) AS type_expense, 
                                            IFNULL(cua.user, 0) AS cost_user, 
                                            IFNULL(cua.backup, 0) AS cost_backup , 
                                            IFNULL(cua.economy_scale, 0) AS cost_economy_scale, 
                                            IFNULL(cua.multiproduct, 0) AS cost_multiproduct, 
                                            IFNULL(cua.sale_objectives, 0) AS sale_objectives, 
                                            IFNULL(cua.quote_payment_method, 0) AS quote_payment_method, 
                                            IFNULL(cua.quote_company, 0) AS quote_company, 
                                            IFNULL(cua.quote_contact, 0) AS quote_contact, 
                                            IFNULL(cua.price, 0) AS price, 
                                            IFNULL(cua.custom_price, 0) AS custom_price, 
                                            IFNULL(cua.type_custom_price, 0) AS type_custom_price, 
                                            IFNULL(cua.analysis_material, 0) AS analysis_material, 
                                            IFNULL(cua.simulator, 0) AS simulator, 
                                            IFNULL(cua.historical, 0) AS historical, 
                                            IFNULL(cua.support, 0) AS support, 
                                            IFNULL(cua.quote, 0) AS quote, 
                                        -- Accesos Compañia
                                            IFNULL(cl.date_contract, 0) AS date_contract,
                                            cl.cost_price_usd AS plan_cost_price_usd, 
                                            cl.flag_composite_product, 
                                            cl.flag_economy_scale, 
                                            cl.flag_sales_objective, 
                                            cl.flag_production_center, 
                                            cl.flag_employee, 
                                            cl.flag_indirect, 
                                            cl.inyection, 
                                            cl.cost_historical AS plan_cost_historical, 
                                            cl.flag_expense, 
                                            cl.flag_family, 
                                            cl.flag_type_price,
                                        -- Accesos de plan
                                            pa.cost_price AS plan_cost_price, 
                                            pa.custom_price AS plan_custom_price, 
                                            pa.cost_analysis_material AS plan_cost_analysis_material, 
                                            pa.cost_simulator AS plan_cost_simulator, 
                                            pa.cost_support AS plan_cost_support, 
                                            pa.cost_quote AS plan_cost_quote, 
                                            pa.cost_economy_scale AS plan_cost_economy_sale, 
                                            -- pa.cost_sale_objectives AS plan_sale_objectives, 
                                            pa.cost_multiproduct AS plan_cost_multiproduct, 
                                        -- Información de usuario
                                            u.firstname, 
                                            u.lastname, 
                                            u.email
                                    FROM users u
                                        LEFT JOIN cost_users_access cua ON cua.id_user = u.id_user
                                        INNER JOIN companies_licenses cl ON cl.id_company = u.id_company
                                        INNER JOIN plans_access pa ON pa.id_plan = cl.plan
                                    WHERE u.id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);
        $userAccess = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $this->logger->notice("usuario Obtenido", array('usuario' => $userAccess));
        return $userAccess;
    }

    public function setGeneralAccess($id_user)
    {
        $userAccess = $this->findUserAccessByUser($id_user);

        $_SESSION['cost_product'] = $userAccess['cost_product'];
        $_SESSION['cost_material'] = $userAccess['cost_material'];
        $_SESSION['cost_machine'] = $userAccess['cost_machine'];
        $_SESSION['cost_process'] = $userAccess['cost_process'];
        $_SESSION['cost_products_material'] = $userAccess['cost_products_material'];
        // $_SESSION['cost_products_process'] = $userAccess['cost_products_process'];
        $_SESSION['factory_load'] = $userAccess['factory_load'];
        // $_SESSION['external_service'] = $userAccess['external_service'];
        $_SESSION['payroll_load'] = $userAccess['payroll_load'];
        $_SESSION['type_payroll'] = $userAccess['type_payroll'];

        // if ($userAccess['flag_expense'] == 2) {
        // $_SESSION['expense'] = 0;
        // } else {
        $_SESSION['expense'] = $userAccess['expense'];
        // }

        $_SESSION['expense_distribution'] = $userAccess['expense_distribution'];
        $_SESSION['production_center'] = $userAccess['production_center'];
        $_SESSION['type_expense'] = $userAccess['type_expense'];
        $_SESSION['flag_expense'] = $userAccess['flag_expense'];
        $_SESSION['flag_expense_distribution'] = $userAccess['flag_family'];
        $_SESSION['flag_type_price'] = $userAccess['flag_type_price'];
        $_SESSION['cost_user'] = $userAccess['cost_user'];
        $_SESSION['cost_backup'] = $userAccess['cost_backup'];
        $_SESSION['cost_economy_scale'] = $userAccess['cost_economy_scale'];
        $_SESSION['sale_objectives'] = $userAccess['sale_objectives'];
        $_SESSION['quote_payment_method'] = $userAccess['quote_payment_method'];
        $_SESSION['quote_company'] = $userAccess['quote_company'];
        $_SESSION['quote_contact'] = $userAccess['quote_contact'];
        $_SESSION['price'] = $userAccess['price'];
        // $_SESSION['price_usd'] = $userAccess['price_usd'];
        $_SESSION['flag_employee'] = $userAccess['flag_employee'];
        $_SESSION['custom_price'] = $userAccess['custom_price'];
        $_SESSION['type_custom_price'] = $userAccess['type_custom_price'];
        $_SESSION['plan_custom_price'] = $userAccess['plan_custom_price'];
        $_SESSION['analysis_material'] = $userAccess['analysis_material'];
        $_SESSION['simulator'] = $userAccess['simulator'];
        $_SESSION['historical'] = $userAccess['historical'];
        $_SESSION['cost_multiproduct'] = $userAccess['cost_multiproduct'];
        $_SESSION['support'] = $userAccess['support'];
        $_SESSION['quotes'] = $userAccess['quote'];
        $_SESSION['plan_cost_price'] = $userAccess['plan_cost_price'];
        $_SESSION['plan_cost_price_usd'] = $userAccess['plan_cost_price_usd'];
        $_SESSION['plan_cost_analysis_material'] = $userAccess['plan_cost_analysis_material'];
        $_SESSION['plan_cost_simulator'] = $userAccess['plan_cost_simulator'];
        $_SESSION['plan_cost_historical'] = $userAccess['plan_cost_historical'];
        $_SESSION['plan_cost_support'] = $userAccess['plan_cost_support'];
        $_SESSION['plan_cost_quote'] = $userAccess['plan_cost_quote'];
        $_SESSION['plan_cost_economy_sale'] = $userAccess['plan_cost_economy_sale'];
        // $_SESSION['plan_sale_objectives'] = $userAccess['plan_sale_objectives'];
        $_SESSION['plan_cost_multiproduct'] = $userAccess['plan_cost_multiproduct'];
        $_SESSION['flag_economy_scale'] = $userAccess['flag_economy_scale'];
        $_SESSION['flag_sales_objective'] = $userAccess['flag_sales_objective'];
        $_SESSION['flag_production_center'] = $userAccess['flag_production_center'];
        $_SESSION['flag_composite_product'] = $userAccess['flag_composite_product'];
        $_SESSION['flag_indirect'] = $userAccess['flag_indirect'];
        $_SESSION['inyection'] = $userAccess['inyection'];
        $_SESSION['contract'] = $userAccess['contract'];
        $_SESSION['date_contract'] = $userAccess['date_contract'];
    }
}
