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
    $stmt = $connection->prepare("SELECT * FROM machines WHERE id_company = :id_company;");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("machines", array('machines' => $machines));
    return $machines;
  }

  /* Buscar si existe maquina en la BD */
  public function findMachine($dataMachine, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT id_machine FROM machines
                                 WHERE machine = :machine AND id_company = :id_company");
    $stmt->execute([
      'machine' => ucfirst(strtolower(trim($dataMachine['machine']))),
      'id_company' => $id_company
    ]);
    $findMachine = $stmt->fetch($connection::FETCH_ASSOC);
    return $findMachine;
  }

  /* Insertar maquina */
  public function insertMachinesByCompany($dataMachine, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $dataMachine = $this->convertData($dataMachine);

    try {
      $stmt = $connection->prepare("INSERT INTO machines (id_company ,machine, cost, years_depreciation, 
                                                residual_value, hours_machine, days_machine) 
                                    VALUES (:id_company ,:machine, :cost, :years_depreciation,
                                        :residual_value, :hours_machine, :days_machine)");
      $stmt->execute([
        'id_company' => $id_company,
        'machine' => strtoupper(trim($dataMachine['machine'])),
        'cost' => trim($dataMachine['costMachine']),
        'years_depreciation' => trim($dataMachine['depreciationYears']),
        'residual_value' => trim($dataMachine['residualValue']),
        'hours_machine' => trim($dataMachine['hoursMachine']),
        'days_machine' => trim($dataMachine['daysMachine'])
      ]);

      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'La referencia ya existe. Ingrese una nueva referencia';

      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar maquina */
  public function updateMachine($dataMachine)
  {
    $connection = Connection::getInstance()->getConnection();

    $dataMachine = $this->convertData($dataMachine);

    try {
      $stmt = $connection->prepare("UPDATE machines SET machine = :machine, cost = :cost, years_depreciation = :years_depreciation,
                                       residual_value = :residual_value , hours_machine = :hours_machine, days_machine = :days_machine   
                                    WHERE id_machine = :id_machine");
      $stmt->execute([
        'id_machine' => trim($dataMachine['idMachine']),
        'machine' => strtoupper(trim($dataMachine['machine'])),
        'cost' => trim($dataMachine['costMachine']),
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

  public function convertData($dataMachine)
  {
    $dataMachine['costMachine'] = str_replace('.', '', $dataMachine['cost']);
    $dataMachine['residualValue'] = str_replace(',', '.', $dataMachine['cost']);
    $dataMachine['costMachine'] = str_replace('.', '', $dataMachine['residualValue']);
    $dataMachine['residualValue'] = str_replace(',', '.', $dataMachine['residualValue']);
    $dataMachine['hoursMachine'] = str_replace('.', '', $dataMachine['hoursMachine']);
    $dataMachine['hoursMachine'] = str_replace(',', '.', $dataMachine['hoursMachine']);

    return $dataMachine;
  }

  public function deleteMachine($id_machine)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT * FROM machines WHERE id_machine = :id_machine");
    $stmt->execute(['id_machine' => trim($id_machine)]);
    $rows = $stmt->rowCount();

    if ($rows > 0) {
      $stmt = $connection->prepare("DELETE FROM machines WHERE id_machine = :id_machine");
      $stmt->execute(['id_machine' => trim($id_machine)]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }
}
