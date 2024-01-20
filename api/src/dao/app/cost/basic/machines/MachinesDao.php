<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MachinesDao
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
    $stmt = $connection->prepare("SELECT m.id_machine, m.id_company, m.machine, m.cost, m.years_depreciation, m.residual_value, m.minute_depreciation, m.hours_machine, m.days_machine, m.cicles_machine, m.cavities, m.unity_time, IFNULL(IF((SELECT id_product_process FROM products_process WHERE id_machine = m.id_machine LIMIT 1) = NULL, 
                                         (SELECT id_manufacturing_load FROM manufacturing_load WHERE id_machine = m.id_machine LIMIT 1), (SELECT id_product_process FROM products_process WHERE id_machine = m.id_machine LIMIT 1)), 0) AS status
                                  FROM machines m
                                  WHERE id_company = :id_company 
                                  ORDER BY machine ASC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("machines", array('machines' => $machines));
    return $machines;
  }

  /* Insertar maquina */
  public function insertMachinesByCompany($dataMachine, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("INSERT INTO machines (id_company ,machine, cost, years_depreciation, 
                                                residual_value, hours_machine, days_machine) 
                                    VALUES (:id_company ,:machine, :cost, :years_depreciation,
                                        :residual_value, :hours_machine, :days_machine)");
      $stmt->execute([
        'id_company' => $id_company,
        'machine' => strtoupper(trim($dataMachine['machine'])),
        'cost' => trim($dataMachine['cost']),
        'years_depreciation' => trim($dataMachine['depreciationYears']),
        'residual_value' => trim($dataMachine['residualValue']),
        'hours_machine' => trim($dataMachine['hoursMachine']),
        'days_machine' => trim($dataMachine['daysMachine'])
      ]);

      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      return $message;
    }
  }

  /* Actualizar maquina */
  public function updateMachine($dataMachine)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("UPDATE machines SET machine = :machine, cost = :cost, years_depreciation = :years_depreciation,
                                       residual_value = :residual_value , hours_machine = :hours_machine, days_machine = :days_machine   
                                    WHERE id_machine = :id_machine");
      $stmt->execute([
        'id_machine' => trim($dataMachine['idMachine']),
        'machine' => strtoupper(trim($dataMachine['machine'])),
        'cost' => trim($dataMachine['cost']),
        'years_depreciation' => trim($dataMachine['depreciationYears']),
        'residual_value' => trim($dataMachine['residualValue']),
        'hours_machine' => trim($dataMachine['hoursMachine']),
        'days_machine' => trim($dataMachine['daysMachine'])
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Eliminar Maquina */
  public function deleteMachine($id_machine)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("SELECT * FROM machines WHERE id_machine = :id_machine");
      $stmt->execute(['id_machine' => $id_machine]);
      $rows = $stmt->rowCount();

      if ($rows > 0) {
        $stmt = $connection->prepare("DELETE FROM machines WHERE id_machine = :id_machine");
        $stmt->execute(['id_machine' => $id_machine]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
      }
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'Maquina asociada a un proceso. No es posible eliminar';

      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
