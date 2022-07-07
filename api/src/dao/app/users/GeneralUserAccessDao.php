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
                                              u.firstname, u.lastname, u.email, cua.create_product AS cost_product, cua.create_materials AS cost_material, cua.create_machines AS cost_machine, cua.create_process AS cost_process, cua.product_materials AS cost_products_material, 
                                              cua.product_process AS cost_products_process, cua.factory_load, cua.external_service, cua.product_line, cua.payroll_load, cua.expense, cua.expense_distribution, cua.user AS cost_user, cua.price, cua.analysis_material, 
                                              cua.tool, pua.create_mold, pua.create_product AS planning_product, pua.create_material AS planning_material, pua.create_machine AS planning_machine, pua.create_process AS planning_process, pua.products_material AS planning_products_material, 
                                              pua.products_process AS planning_products_process, pua.programs_machine, pua.cicles_machine, pua.inv_category, pua.sale, pua.user AS planning_user, pua.inventory, pua.plan_order, pua.programming, pua.plan_load, pua.explosion_of_material, pua.office
                                      FROM users u
                                        INNER JOIN cost_users_access cua ON cua.id_user = u.id_user
                                        INNER JOIN planning_user_access pua ON pua.id_user = u.id_user
                                      WHERE u.id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);
        $userAccess = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $this->logger->notice("usuario Obtenido", array('usuario' => $userAccess));
        return $userAccess;
    }
}
