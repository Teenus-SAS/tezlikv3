<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PlanMaterialsDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllMaterialsByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM materials WHERE id_company = :id_company");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("materials", array('materials' => $materials));
    return $materials;
  }

  /* Consultar si existe materia prima en la BD */
  public function findMaterial($dataMaterial, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT id_material FROM materials 
                                  WHERE reference = :reference 
                                  AND material = :material 
                                  AND id_company = :id_company");
    $stmt->execute([
      'reference' => trim($dataMaterial['refRawMaterial']),
      'material' => ucfirst(strtolower(trim($dataMaterial['nameRawMaterial']))),
      'id_company' => $id_company,
    ]);
    $findMaterial = $stmt->fetch($connection::FETCH_ASSOC);
    return $findMaterial;
  }

  /* Insertar materia prima */
  public function insertMaterialsByCompany($dataMaterial, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $costRawMaterial = str_replace('.', '', $dataMaterial['costRawMaterial']);

    try {
      $stmt = $connection->prepare("INSERT INTO materials (id_company ,reference, material, unit, cost) 
                                      VALUES(:id_company ,:reference, :material, :unit, :cost)");
      $stmt->execute([
        'id_company' => $id_company,
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => ucfirst(strtolower(trim($dataMaterial['nameRawMaterial']))),
        'unit' => ucfirst(strtolower(trim($dataMaterial['unityRawMaterial']))),
        'cost' => $costRawMaterial
      ]);

      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'Referencia duplicada. Ingrese una nueva referencia';

      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar materia prima  */
  public function updateMaterialsByCompany($dataMaterial)
  {
    $connection = Connection::getInstance()->getConnection();
    $costRawMaterial = str_replace('.', '', $dataMaterial['costRawMaterial']);

    try {
      $stmt = $connection->prepare("UPDATE materials SET reference = :reference, material = :material, unit = :unit, cost = :cost 
                                    WHERE id_material = :id_material");
      $stmt->execute([
        'id_material' => $dataMaterial['idMaterial'],
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => ucfirst(strtolower(trim($dataMaterial['nameRawMaterial']))),
        'unit' => ucfirst(strtolower(trim($dataMaterial['unityRawMaterial']))),
        'cost' => $costRawMaterial
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteMaterial($dataMaterial)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT * FROM materials WHERE id_material = :id_material");
    $stmt->execute(['id_material' => $dataMaterial['idMaterial']]);
    $rows = $stmt->rowCount();

    if ($rows > 0) {
      $stmt = $connection->prepare("DELETE FROM materials WHERE id_material = :id_material");
      $stmt->execute(['id_material' => $dataMaterial['idMaterial']]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }
}
