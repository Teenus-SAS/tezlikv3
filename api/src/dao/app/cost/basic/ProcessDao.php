<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProcessDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllProcessByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM process WHERE id_company = :id_company;");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $process = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("process", array('process' => $process));
    return $process;
  }

  public function findProcess($dataProcess, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT id_process FROM process
                                  WHERE process = :process AND id_company = :id_company");
    $stmt->execute([
      'process' => ucfirst(strtolower(trim($dataProcess['process']))),
      'id_company' => $id_company
    ]);
    $findProcess = $stmt->fetch($connection::FETCH_ASSOC);
    return $findProcess;
  }

  public function insertProcessByCompany($dataProcess, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("INSERT INTO process (id_company ,process) VALUES (:id_company ,:process)");
      $stmt->execute([
        'id_company'  => $id_company,
        'process' => ucfirst(strtolower(trim($dataProcess['process'])))
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'Proceso duplicado. Ingrese una nuevo proceso';

      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function updateProcess($dataProcess)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("UPDATE process SET process = :process WHERE id_process = :id_process");
      $stmt->execute([
        'process' => ucfirst(strtolower(trim($dataProcess['process']))),
        'id_process' => $dataProcess['idProcess'],
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteProcess($id_process)
  {
    $connection = Connection::getInstance()->getConnection();
    try {
      $stmt = $connection->prepare("SELECT * FROM process WHERE id_process = :id_process");
      $stmt->execute(['id_process' => $id_process]);
      $rows = $stmt->rowCount();

      if ($rows > 0) {
        $stmt = $connection->prepare("DELETE FROM process WHERE id_process = :id_process");
        $stmt->execute(['id_process' => $id_process]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
      }
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'Proceso asociado a un producto/nomina. Imposible Eliminar';

      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
