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

        $stmt = $connection->prepare("SELECT 
                                            IFNULL(cua.create_product, 0) AS cost_product, IFNULL(cua.create_materials, 0) AS cost_material, IFNULL(cua.create_machines, 0) AS cost_machine, IFNULL(cua.create_process, 0) AS cost_process, IFNULL(cua.product_materials, 0) AS cost_products_material, IFNULL(cua.product_process, 0) AS cost_products_process,  IFNULL(cua.factory_load, 0) AS factory_load,  IFNULL(cua.external_service, 0) AS external_service, IFNULL(cua.payroll_load, 0) AS payroll_load, 
                                            IFNULL(cua.expense, 0) AS expense, IFNULL(cua.expense_distribution, 0) AS expense_distribution, IFNULL(cua.user, 0) AS cost_user, IFNULL(cua.price, 0) AS price, IFNULL(cua.analysis_material, 0) AS analysis_material, IFNULL(cua.tool, 0) AS tool, IFNULL(cua.quote, 0) AS quote, IFNULL(pua.create_mold, 0) AS create_mold, IFNULL(pua.create_product, 0) AS planning_product, IFNULL(pua.create_material, 0) AS planning_material, 
                                            IFNULL(pua.create_machine, 0) AS planning_machine, IFNULL(pua.create_process, 0) AS planning_process, IFNULL(pua.products_material, 0) AS planning_products_material, IFNULL(pua.products_process, 0) AS planning_products_process, IFNULL(pua.programs_machine, 0) AS programs_machine, IFNULL(pua.cicles_machine, 0) AS cicles_machine, IFNULL(pua.inv_category, 0) AS inv_category, IFNULL(pua.sale, 0) AS sale, IFNULL(pua.user, 0) AS planning_user, 
                                            IFNULL(pua.client, 0) AS client, IFNULL(pua.orders_type, 0) AS orders_type, IFNULL(pua.inventory, 0) AS inventory, IFNULL(pua.plan_order, 0) AS plan_order, IFNULL(pua.program, 0) AS program, IFNULL(pua.plan_load, 0) AS plan_load, IFNULL(pua.explosion_of_material, 0) AS explosion_of_material, IFNULL(pua.office, 0) AS office, pa.cost_price AS plan_cost_price, 
                                            pa.cost_analysis_material AS plan_cost_analysis_material, pa.cost_tool AS plan_cost_tool, pa.cost_quote AS plan_cost_quote, pa.plan_order AS plan_planning_order, pa.plan_inventory AS plan_planning_inventory, pa.plan_program AS plan_planning_program, pa.plan_load AS plan_planning_load, pa.plan_explosion_of_material AS plan_planning_explosion_of_material, pa.plan_office AS plan_planning_office
                                      FROM users u
                                          LEFT JOIN cost_users_access cua ON cua.id_user = u.id_user
                                          LEFT JOIN planning_user_access pua ON pua.id_user = u.id_user
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
        $_SESSION['cost_products_process'] = $userAccess['cost_products_process'];
        $_SESSION['factory_load'] = $userAccess['factory_load'];
        $_SESSION['external_service'] = $userAccess['external_service'];
        $_SESSION['payroll_load'] = $userAccess['payroll_load'];
        $_SESSION['expense'] = $userAccess['expense'];
        $_SESSION['expense_distribution'] = $userAccess['expense_distribution'];
        $_SESSION['cost_user'] = $userAccess['cost_user'];
        $_SESSION['price'] = $userAccess['price'];
        $_SESSION['analysis_material'] = $userAccess['analysis_material'];
        $_SESSION['tool'] = $userAccess['tool'];
        $_SESSION['quotes'] = $userAccess['quote'];
        $_SESSION['create_mold'] = $userAccess['create_mold'];
        $_SESSION['planning_product'] = $userAccess['planning_product'];
        $_SESSION['planning_material'] = $userAccess['planning_material'];
        $_SESSION['planning_machine'] = $userAccess['planning_machine'];
        $_SESSION['planning_process'] = $userAccess['planning_process'];
        $_SESSION['planning_products_material'] = $userAccess['planning_products_material'];
        $_SESSION['planning_products_process'] = $userAccess['planning_products_process'];
        $_SESSION['programs_machine'] = $userAccess['programs_machine'];
        $_SESSION['cicles_machine'] = $userAccess['cicles_machine'];
        $_SESSION['inv_category'] = $userAccess['inv_category'];
        $_SESSION['sale'] = $userAccess['sale'];
        $_SESSION['planning_user'] = $userAccess['planning_user'];
        $_SESSION['client'] = $userAccess['client'];
        $_SESSION['orders_type'] = $userAccess['orders_type'];
        $_SESSION['inventory'] = $userAccess['inventory'];
        $_SESSION['plan_order'] = $userAccess['plan_order'];
        $_SESSION['program'] = $userAccess['program'];
        $_SESSION['plan_load'] = $userAccess['plan_load'];
        $_SESSION['explosion_of_material'] = $userAccess['explosion_of_material'];
        $_SESSION['office'] = $userAccess['office'];
        // $_SESSION['plan_cost_price'] = $userAccess['plan_cost_price'];
        $_SESSION['plan_cost_analysis_material'] = $userAccess['plan_cost_analysis_material'];
        $_SESSION['plan_cost_tool'] = $userAccess['plan_cost_tool'];
        $_SESSION['plan_cost_quote'] = $userAccess['plan_cost_quote'];
        $_SESSION['plan_planning_order'] = $userAccess['plan_planning_order'];
        $_SESSION['plan_planning_inventory'] = $userAccess['plan_planning_inventory'];
        $_SESSION['plan_planning_program'] = $userAccess['plan_planning_program'];
        $_SESSION['plan_planning_load'] = $userAccess['plan_planning_load'];
        $_SESSION['plan_planning_explosion_of_material'] = $userAccess['plan_planning_explosion_of_material'];
        $_SESSION['plan_planning_office'] = $userAccess['plan_planning_office'];
    }
}
