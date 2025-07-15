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

    public function findAllPlanCiclesMachine($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pcm.id_cicles_machine, p.product, m.machine, pcm.cicles_hour 
                                      FROM plan_cicles_machine pcm
                                       INNER JOIN machines m ON m.id_machine = pcm.id_machine
                                       INNER JOIN products p ON p.id_product = pcm.id_product
                                      WHERE pcm.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $planCiclesMachines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $planCiclesMachines;
    }

    // Buscar si existe en la BD
    public function findPlanCiclesMachine($dataCiclesMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT *  FROM plan_cicles_machine
                                      WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataCiclesMachine['idProduct'],
            'id_company' => $id_company
        ]);
        $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }

    public function addPlanCiclesMachines($dataCiclesMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $ciclesHour = str_replace('.', '', $dataCiclesMachine['ciclesHour']);

        try {
            $stmt = $connection->prepare("INSERT INTO plan_cicles_machine (id_product, id_machine, id_company, cicles_hour) 
                                          VALUES(:id_product, :id_machine, :id_company, :cicles_hour)");
            $stmt->execute([
                'id_product' => $dataCiclesMachine['idProduct'],
                'id_machine' => $dataCiclesMachine['idMachine'],
                'id_company' => $id_company,
                'cicles_hour' => $ciclesHour
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updatePlanCiclesMachine($dataCiclesMachine)
    {
        $connection = Connection::getInstance()->getConnection();

        $ciclesHour = str_replace('.', '', $dataCiclesMachine['ciclesHour']);

        try {
            $stmt = $connection->prepare("UPDATE plan_cicles_machine SET id_product = :id_product, id_machine = :id_machine, cicles_hour = :cicles_hour 
                                          WHERE id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'id_cicles_machine' => $dataCiclesMachine['idCiclesMachine'],
                'id_product' => $dataCiclesMachine['idProduct'],
                'id_machine' => $dataCiclesMachine['idMachine'],
                'cicles_hour' => $ciclesHour
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deletePlanCiclesMachine($id_cicles_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_cicles_machine WHERE id_cicles_machine = :id_cicles_machine");
        $stmt->execute(['id_cicles_machine' => $id_cicles_machine]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM plan_cicles_machine WHERE id_cicles_machine = :id_cicles_machine");
            $stmt->execute(['id_cicles_machine' => $id_cicles_machine]);
        }
    }
}
