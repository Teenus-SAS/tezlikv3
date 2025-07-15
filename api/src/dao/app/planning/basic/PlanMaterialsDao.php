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
    $stmt = $connection->prepare("SELECT * FROM materials WHERE id_company = :id_company ORDER BY material ASC");
    $stmt->execute(['id_company' => $id_company]);



    $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

    return $materials;
  }

  /* Insertar materia prima */
  public function insertMaterialsByCompany($dataMaterial, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $quantity = str_replace('.', '', $dataMaterial['quantity']);
    $quantity = str_replace(',', '.', $quantity);

    $dataMaterial['category'] == 'Insumos' ? $dataMaterial['category'] = 1 : $dataMaterial['category'];
    $dataMaterial['category'] == 'Materiales' ? $dataMaterial['category'] = 2 : $dataMaterial['category'];

    try {
      $stmt = $connection->prepare("INSERT INTO materials (id_company ,reference, material, unit, quantity, category) 
                                      VALUES(:id_company ,:reference, :material, :unit, :quantity, :category)");
      $stmt->execute([
        'id_company' => $id_company,
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
        'unit' => strtoupper(trim($dataMaterial['unityRawMaterial'])),
        'quantity' => $quantity,
        'category' => $dataMaterial['category']
      ]);
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'La referencia ya existe. Ingrese una nueva referencia';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar materia prima  */
  public function updateMaterialsByCompany($dataMaterial)
  {
    $connection = Connection::getInstance()->getConnection();

    $quantity = str_replace('.', '', $dataMaterial['quantity']);
    $quantity = str_replace(',', '.', $quantity);

    $dataMaterial['category'] == 'Insumos' ? $dataMaterial['category'] = 1 : $dataMaterial['category'];
    $dataMaterial['category'] == 'Materiales' ? $dataMaterial['category'] = 2 : $dataMaterial['category'];

    try {
      $stmt = $connection->prepare("UPDATE materials SET reference = :reference, material = :material, unit = :unit, quantity = :quantity, category = :category
                                    WHERE id_material = :id_material");
      $stmt->execute([
        'id_material' => $dataMaterial['idMaterial'],
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
        'unit' => strtoupper(trim($dataMaterial['unityRawMaterial'])),
        'quantity' => $quantity,
        'category' => $dataMaterial['category']
      ]);
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
