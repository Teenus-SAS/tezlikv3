<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MaterialsDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  /* Insertar materia prima */
  public function insertMaterialsByCompany($dataMaterial, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    // $costRawMaterial = str_replace('.', '', $dataMaterial['costRawMaterial']);
    // $costRawMaterial = str_replace(',', '.', $costRawMaterial);

    try {
      $stmt = $connection->prepare("INSERT INTO materials (id_company ,reference, material, unit, cost) 
                                      VALUES(:id_company ,:reference, :material, :unit, :cost)");
      $stmt->execute([
        'id_company' => $id_company,
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
        'unit' => $dataMaterial['unit'],
        'cost' => $dataMaterial['costRawMaterial']
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

  /* Actualizar materia prima  */
  public function updateMaterialsByCompany($dataMaterial, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    // $costRawMaterial = str_replace('.', '', $dataMaterial['costRawMaterial']);
    // $costRawMaterial = str_replace(',', '.', $costRawMaterial);

    try {
      $stmt = $connection->prepare("UPDATE materials SET reference = :reference, material = :material, unit = :unit, cost = :cost 
                                    WHERE id_material = :id_material AND id_company = :id_company");
      $stmt->execute([
        'id_material' => $dataMaterial['idMaterial'],
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
        'unit' => $dataMaterial['unit'],
        'cost' => $dataMaterial['costRawMaterial'],
        'id_company' => $id_company
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
