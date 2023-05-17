<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PucDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllCountsPUC()
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM puc ORDER BY CAST(SUBSTRING(number_count, 1, 2) AS UNSIGNED), CAST(SUBSTRING(number_count, 1, 4) AS UNSIGNED), CAST(SUBSTRING(number_count, 1, 5) AS UNSIGNED);");
    $stmt->execute();

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $puc = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("process", array('process' => $puc));
    return $puc;
  }

  // Consultar si existe la cuenta en BD
  public function findPuc($dataPuc)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT id_puc FROM puc WHERE number_count = :number_count AND count = :count");
    $stmt->execute([
      'number_count' => trim($dataPuc['numberCount']),
      'count' => ucfirst(strtolower(trim($dataPuc['count'])))
    ]);
    $findPuc = $stmt->fetch($connection::FETCH_ASSOC);
    return $findPuc;
  }

  public function insertPuc($dataPuc)
  {
    $connection = Connection::getInstance()->getConnection();
    try {
      $stmt = $connection->prepare("INSERT INTO puc (number_count, count) VALUES (:number_count, :count)");
      $stmt->execute([
        'number_count' => trim($dataPuc['numberCount']),
        'count' => ucfirst(strtolower(trim($dataPuc['count'])))
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      if ($e->getCode() == 23000)
        $message = 'Numero de cuenta duplicada. Ingrese un nuevo numero';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function updatePuc($dataPuc)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("UPDATE puc SET number_count = :number_count, count = :count
                                    WHERE id_puc = :id_puc");
      $stmt->execute([
        'id_puc' => trim($dataPuc['idPuc']),
        'number_count' => trim($dataPuc['numberCount']),
        'count' => ucfirst(strtolower(trim($dataPuc['count'])))
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deletePuc($id_puc)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT * FROM puc WHERE id_puc = :id_puc");
    $stmt->execute(['id_puc' => $id_puc]);
    $rows = $stmt->rowCount();

    if ($rows > 0) {
      $stmt = $connection->prepare("DELETE FROM puc WHERE id_puc = :id_puc");
      $stmt->execute(['id_puc' => $id_puc]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }
}
