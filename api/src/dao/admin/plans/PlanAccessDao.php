<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PlanAccessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllPlansAccess()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plans_access");
        $stmt->execute();

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $plans = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $plans;
    }

    public function findPlanAccess($id_plan)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plans_access WHERE id_plan = :id_plan");
        $stmt->execute(['id_plan' => $id_plan]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $plans = $stmt->fetch($connection::FETCH_ASSOC);

        return $plans;
    }

    public function updateAccessPlan($dataPlan)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE plans_access SET cant_products = :cant_products, cost_price= :cost_price, cost_analysis_material= :cost_analysis_material, cost_support= :cost_support, cost_quote = :cost_quote, cost_economy_scale = :cost_economy_scale, 
                                                                  plan_inventory = :plan_inventory, plan_order= :plan_order, plan_program= :plan_program, plan_load= :plan_load, plan_explosion_of_material= :plan_explosion_of_material, plan_office= :plan_office
                                          WHERE id_plan= :id_plan");
            $stmt->execute([
                'id_plan' => $dataPlan['idPlan'],                                   'cost_economy_scale' => $dataPlan['economyScale'],
                'cant_products' => $dataPlan['cantProducts'],                       'plan_inventory' => $dataPlan['inventories'],
                'cost_price' => $dataPlan['prices'],                                'plan_order' => $dataPlan['orders'],
                'cost_analysis_material' => $dataPlan['analysisRawMaterials'],      'plan_program' => $dataPlan['programming'],
                'plan_load' => $dataPlan['loads'],                                  'plan_explosion_of_material' => $dataPlan['explosionOfMaterials'],
                'cost_support' => $dataPlan['support'],                             'plan_office' =>  $dataPlan['offices'],
                'cost_quote' => $dataPlan['quotes']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);

            return $error;
        }
    }
}