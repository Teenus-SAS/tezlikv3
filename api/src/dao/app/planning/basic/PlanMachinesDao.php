<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PlanMachinesDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllMachinesByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT m.id_machine, m.machine, m.hours_machine, m.days_machine, pcm.cicles_hour 
                                  FROM machines m
                                   LEFT JOIN plan_cicles_machine pcm ON pcm.id_machine = m.id_machine 
                                  WHERE m.id_company = :id_company;");
    $stmt->execute(['id_company' => $id_company]);



    $machines = $stmt->fetchAll($connection::FETCH_ASSOC);

    return $machines;
  }

  /* Insertar maquina */
  public function insertMachinesByCompany($dataMachine, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("INSERT INTO machines (id_company ,machine) 
                                    VALUES (:id_company ,:machine)");
      $stmt->execute([
        'id_company' => $id_company,
        'machine' => strtoupper($dataMachine['machine'])
      ]);
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'Maquina duplicada. Ingrese una nueva maquina';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar maquina */
  public function updateMachine($dataMachine)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("UPDATE machines SET machine = :machine WHERE id_machine = :id_machine");
      $stmt->execute([
        'id_machine' => $dataMachine['idMachine'],
        'machine' => strtoupper($dataMachine['machine'])
      ]);
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
