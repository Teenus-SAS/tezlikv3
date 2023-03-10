<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralMaterialsDao
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
        $stmt = $connection->prepare("SELECT m.id_material, m.reference, m.material, mg.id_magnitude, mg.magnitude, 
                                             u.id_unit, u.abbreviation, m.cost, m.category, m.quantity
                                      FROM materials m
                                          INNER JOIN units u ON u.id_unit = m.unit
                                          INNER JOIN magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE m.id_company = :id_company ORDER BY m.material ASC");
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
                                    OR material = :material 
                                    AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataMaterial['refRawMaterial']),
            'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
            'id_company' => $id_company,
        ]);
        $findMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findMaterial;
    }

    public function findMaterialAndUnits($id_material, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT m.id_material, m.reference, m.material, mg.id_magnitude, mg.magnitude, 
                                             u.id_unit, u.abbreviation, m.cost, m.category, m.quantity
                                      FROM materials m
                                        INNER JOIN units u ON u.id_unit = m.unit
                                        INNER JOIN magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE m.id_material = :id_material AND id_company = :id_company");
        $stmt->execute([
            'id_material' => $id_material,
            'id_company' => $id_company,
        ]);
        $findMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findMaterial;
    }

    public function deleteMaterial($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM materials WHERE id_material = :id_material");
            $stmt->execute(['id_material' => $id_material]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM materials WHERE id_material = :id_material");
                $stmt->execute(['id_material' => $id_material]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Esta materia prima no se puede eliminar, esta configurada a un producto';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
