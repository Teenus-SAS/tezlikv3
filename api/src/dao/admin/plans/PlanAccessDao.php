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
            $stmt = $connection->prepare("UPDATE plans_access SET cant_products = :cant_products, cost_price= :cost_price, cost_price_usd= :cost_price_usd, cost_analysis_material= :cost_analysis_material, cost_support= :cost_support, 
                                                                  cost_quote = :cost_quote, cost_multiproduct = :cost_multiproduct, cost_economy_scale = :cost_economy_scale, cost_simulator = :cost_simulator
                                          WHERE id_plan= :id_plan");
            $stmt->execute([
                'id_plan' => $dataPlan['idPlan'],                                   'cost_support' => $dataPlan['support'],
                'cant_products' => $dataPlan['cantProducts'],                       'cost_quote' => $dataPlan['quotes'],
                'cost_price' => $dataPlan['prices'],                                'cost_multiproduct' => $dataPlan['multiproduct'],
                'cost_price_usd' => $dataPlan['pricesUSD'],                        'cost_economy_scale' => $dataPlan['economyScale'],
                'cost_analysis_material' => $dataPlan['analysisRawMaterials'],      'cost_simulator' => $dataPlan['simulator'],
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);

            return $error;
        }
    }
}
