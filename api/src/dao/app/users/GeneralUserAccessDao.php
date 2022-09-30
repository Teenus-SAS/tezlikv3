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

        $stmt = $connection->prepare("SELECT u.firstname, u.lastname, u.email, IFNULL(cua.create_product, 0) AS cost_product, IFNULL(cua.create_materials, 0) AS cost_material, IFNULL(cua.create_machines, 0) AS cost_machine, 
                                                    IFNULL(cua.create_process, 0) AS cost_process, IFNULL(cua.product_materials, 0) AS cost_products_material, IFNULL(cua.product_process, 0) AS cost_products_process, IFNULL(cua.factory_load, 0) AS factory_load, 
                                             IFNULL(cua.external_service, 0) AS external_service, IFNULL(cua.payroll_load, 0) AS payroll_load, IFNULL(cua.expense, 0) AS expense, IFNULL(cua.expense_distribution, 0) AS expense_distribution, IFNULL(cua.user, 0) AS cost_user, 
                                             IFNULL(cua.price, 0) AS price, IFNULL(cua.analysis_material, 0) AS analysis_material, IFNULL(cua.tool, 0) AS tool, IFNULL(pua.create_mold, 0) AS create_mold, IFNULL(pua.create_product, 0) AS planning_product, IFNULL(pua.create_material, 0) AS planning_material,
                                             IFNULL(pua.create_machine, 0) AS planning_machine, IFNULL(pua.create_process, 0) AS planning_process, IFNULL(pua.products_material, 0) AS planning_products_material, IFNULL(pua.products_process, 0) AS planning_products_process, 
                                             IFNULL(pua.programs_machine, 0) AS programs_machine, IFNULL(pua.cicles_machine, 0) AS cicles_machine, IFNULL(pua.inv_category, 0) AS inv_category, IFNULL(pua.sale, 0) AS sale, IFNULL(pua.user, 0) AS planning_user, IFNULL(pua.client, 0) AS client, 
                                             IFNULL(pua.orders_type, 0) AS orders_type, IFNULL(pua.inventory, 0) AS inventory, IFNULL(pua.plan_order, 0) AS plan_order, IFNULL(pua.program, 0) AS program, IFNULL(pua.plan_load, 0) AS plan_load, IFNULL(pua.explosion_of_material, 0) AS explosion_of_material, IFNULL(pua.office, 0) AS office
                                      FROM users u
                                        LEFT JOIN cost_users_access cua ON cua.id_user = u.id_user
                                        LEFT JOIN planning_user_access pua ON pua.id_user = u.id_user
                                      WHERE u.id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);
        $userAccess = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $this->logger->notice("usuario Obtenido", array('usuario' => $userAccess));
        return $userAccess;
    }
}
