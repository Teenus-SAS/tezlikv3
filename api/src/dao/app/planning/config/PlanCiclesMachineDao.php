<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PlanCiclesMachineDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findPlanCiclesMachine($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pcm.id_cicles_machine, p.reference, m.machine, pcm.cicles_hour 
                                      FROM plan_cicles_machine pcm
                                       INNER JOIN machines m ON m.id_machine = pcm.id_machine
                                       INNER JOIN products p ON p.id_product = pcm.id_product
                                      WHERE pcm.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $planCiclesMachine = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }

    public function addPlanCiclesMachines($dataCiclesMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        //$dataCiclesMachine['idProduct'] ? $dataCiclesMachine['idProduct'] : $dataCiclesMachine['idProduct'] = 0;

        try {
            $stmt = $connection->prepare("INSERT INTO plan_cicles_machine (id_machine, id_company, cicles_hour)
                                      VALUES(:id_machine, :id_company, :cicles_hour)");
            $stmt->execute([
                //'id_product' => $dataCiclesMachine['idProduct'],
                'id_machine' => $dataCiclesMachine['idMachine'],
                'id_company' => $id_company,
                'cicles_hour' => $dataCiclesMachine['ciclesHour']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
