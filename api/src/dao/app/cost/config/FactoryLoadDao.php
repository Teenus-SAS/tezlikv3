<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FactoryLoadDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllFactoryLoadByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT ml.id_manufacturing_load, ml.id_machine, m.machine, ml.input, ml.cost, ml.cost_minute 
                                  FROM manufacturing_load ml
                                  INNER JOIN machines m ON ml.id_machine = m.id_machine
                                  WHERE ml.id_company = :id_company;");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $factoryloads = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("factory load", array('factory load' => $factoryloads));
    return $factoryloads;
  }

  // Consultar si existe carga fabril en BD

  public function insertFactoryLoadByCompany($dataFactoryLoad, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $costFactory = str_replace('.', '', $dataFactoryLoad['costFactory']);

    try {
      $stmt = $connection->prepare("INSERT INTO manufacturing_load (id_machine, id_company, input, cost)
                                    VALUES (:id_machine, :id_company, :input, :cost)");
      $stmt->execute([
        'id_machine' => $dataFactoryLoad['idMachine'],
        'id_company' => $id_company,
        'input' => strtoupper(trim($dataFactoryLoad['descriptionFactoryLoad'])),
        'cost' => $costFactory,
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function updateFactoryLoad($dataFactoryLoad)
  {
    $connection = Connection::getInstance()->getConnection();

    $costFactory = str_replace('.', '', $dataFactoryLoad['costFactory']);

    try {
      $stmt = $connection->prepare("UPDATE manufacturing_load SET id_machine = :id_machine, input = :input, cost = :cost
                                    WHERE id_manufacturing_load = :id_manufacturing_load");
      $stmt->execute([
        'id_manufacturing_load' => $dataFactoryLoad['idManufacturingLoad'],
        'id_machine' => $dataFactoryLoad['idMachine'],
        'input' => strtoupper(trim($dataFactoryLoad['descriptionFactoryLoad'])),
        'cost' => $costFactory
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('error' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteFactoryLoad($dataFactoryLoad)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT * FROM manufacturing_load WHERE id_manufacturing_load = :id_manufacturing_load");
    $stmt->execute(['id_manufacturing_load' => $dataFactoryLoad['idManufacturingLoad']]);
    $rows = $stmt->rowCount();

    if ($rows > 0) {
      $stmt = $connection->prepare("DELETE FROM manufacturing_load WHERE id_manufacturing_load = :id_manufacturing_load");
      $stmt->execute(['id_manufacturing_load' => $dataFactoryLoad['idManufacturingLoad']]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }
}
