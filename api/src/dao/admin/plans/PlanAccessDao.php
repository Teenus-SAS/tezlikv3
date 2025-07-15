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


        $plans = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $plans;
    }

    public function findPlanAccess($id_plan)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plans_access WHERE id_plan = :id_plan");
        $stmt->execute(['id_plan' => $id_plan]);


        $plans = $stmt->fetch($connection::FETCH_ASSOC);

        return $plans;
    }

    public function updateAccessPlan($dataPlan)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE plans_access SET cant_products = :cant_products, cost_price = :cost_price, custom_price = :custom_price, cost_analysis_material= :cost_analysis_material, cost_support= :cost_support, cost_quote = :cost_quote, 
                                                                  cost_multiproduct = :cost_multiproduct, cost_economy_scale = :cost_economy_scale, cost_sale_objectives = :cost_sale_objectives, cost_price_objectives = :cost_price_objectives, cost_simulator = :cost_simulator
                                          WHERE id_plan= :id_plan");
            $stmt->execute([
                'id_plan' => $dataPlan['idPlan'],
                'cost_sale_objectives' => $dataPlan['salesObjective'],
                'cant_products' => $dataPlan['cantProducts'],
                'cost_price_objectives' => $dataPlan['priceObjective'],
                'cost_price' => $dataPlan['prices'],
                'cost_quote' => $dataPlan['quotes'],
                'custom_price' => $dataPlan['customPrices'],
                'cost_multiproduct' => $dataPlan['multiproduct'],
                'cost_analysis_material' => $dataPlan['analysisRawMaterials'],
                'cost_simulator' => $dataPlan['simulator'],
                'cost_economy_scale' => $dataPlan['economyScale'],
                'cost_support' => $dataPlan['support'],
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);

            return $error;
        }
    }
}
