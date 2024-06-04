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
        $stmt = $connection->prepare("SELECT 
                                            -- Informacion Basica Material
                                                m.reference, 
                                                m.material, 
                                                c.category, 
                                                mg.magnitude,
                                                u.unit,
                                                u.abbreviation,
                                                m.date_material, 
                                                m.quantity, 
                                                m.observation, 
                                                m.img,
                                            -- Ids Material
                                                m.id_material,
                                                c.id_category,
                                                mg.id_magnitude,
                                                u.id_unit,
                                            -- Costos Material
                                                m.cost,
                                                m.cost_usd,
                                                m.cost_import, 
                                                m.cost_import_usd, 
                                                m.cost_export, 
                                                m.cost_export_usd, 
                                                m.cost_total,
                                            -- General
                                                m.flag_usd,
                                                m.flag_indirect,
                                                IFNULL((SELECT id_product_material FROM products_materials WHERE id_material = m.id_material LIMIT 1), 0) AS status
                                      FROM materials m
                                      	  LEFT JOIN categories c ON c.id_category = m.id_category
                                          INNER JOIN convert_units u ON u.id_unit = m.unit
                                          INNER JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE m.id_company = :id_company ORDER BY m.material ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("materials", array('materials' => $materials));
        return $materials;
    }

    public function findAllMaterialsUSDByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM materials 
                                      WHERE id_company = :id_company AND flag_usd = 1");
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
                                        WHERE (reference = :reference AND material = :material) 
                                        AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataMaterial['refRawMaterial']),
            'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
            'id_company' => $id_company,
        ]);
        $materials = $stmt->fetch($connection::FETCH_ASSOC);
        return $materials;
    }

    /* Consultar si existe la referencia o nombre de la materia prima en la BD */
    public function findMaterialByReferenceOrName($dataMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM materials 
                                    WHERE id_company = :id_company AND (reference = :reference OR material = :material)");
        $stmt->execute([
            'reference' => trim($dataMaterial['refRawMaterial']),
            'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
            'id_company' => $id_company,
        ]);
        $findMaterial = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $findMaterial;
    }

    public function findMaterialAndUnits($id_material, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT m.id_material, m.reference, m.material, mg.id_magnitude, mg.magnitude, 
                                             u.id_unit, u.abbreviation, m.cost, m.date_material, m.quantity
                                      FROM materials m
                                        INNER JOIN convert_units u ON u.id_unit = m.unit
                                        INNER JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE m.id_material = :id_material AND id_company = :id_company");
        $stmt->execute([
            'id_material' => $id_material,
            'id_company' => $id_company,
        ]);
        $findMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findMaterial;
    }

    public function findAllProductsByMaterials($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product
                                      FROM products p
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                      WHERE m.id_material = :id_material");
        $stmt->execute([
            'id_material' => $id_material,
        ]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    /* Modificar Costo Ficha Tecnica */
    public function updateCostProductMaterial($dataMaterial, $quantity)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if (($_SESSION['export_import'] == '1' && $_SESSION['flag_export_import'] == '1') && $dataMaterial['cost_total'] > 0)
                $cost = $quantity * $dataMaterial['cost_total'];
            else
                $cost = $quantity * $dataMaterial['cost'];

            if ($_SESSION['flag_currency_usd'] == 1) {
                $cost_usd = $cost / floatval($_SESSION['coverage_usd']);

                $stmt = $connection->prepare("UPDATE products_materials SET cost = :cost, cost_usd = :cost_usd 
                                              WHERE id_product_material = :id_product_material");
                $stmt->execute([
                    'cost' => $cost,
                    'cost_usd' => $cost_usd,
                    'id_product_material' => $dataMaterial['id_product_material']
                ]);
            } else {
                $stmt = $connection->prepare("UPDATE products_materials SET cost = :cost WHERE id_product_material = :id_product_material");
                $stmt->execute([
                    'cost' => $cost,
                    'id_product_material' => $dataMaterial['id_product_material']
                ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Esta materia prima no se puede eliminar, esta configurada a un producto';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function saveBillMaterial($dataMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE materials SET date_material = :date_material, observation = :observation WHERE id_material = :id_material");
            $stmt->execute([
                'date_material' => $dataMaterial['date'],
                'observation' => $dataMaterial['observation'],
                'id_material' => $dataMaterial['idMaterial']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function saveCostsMaterial($dataMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE materials SET cost_import = :cost_import, cost_export = :cost_export, cost_total = :cost_total
                                          WHERE id_material = :id_material");
            $stmt->execute([
                'cost_import' => $dataMaterial['costImport'],
                'cost_export' => $dataMaterial['costExport'],
                'cost_total' => $dataMaterial['costTotal'],
                'id_material' => $dataMaterial['idMaterial']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function saveCostUSDMaterial($dataMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE materials SET cost_usd = :cost_usd WHERE id_material = :id_material");
            $stmt->execute([
                'cost_usd' => $dataMaterial['cost_usd'],
                'id_material' => $dataMaterial['idMaterial']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function saveAllCostsUSDMaterial($dataMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE materials SET cost_export_usd = :cost_export_usd, cost_import_usd = :cost_import_usd
                                          WHERE id_material = :id_material");
            $stmt->execute([
                'cost_export_usd' => $dataMaterial['costImport'],
                'cost_import_usd' => $dataMaterial['costExport'],
                'id_material' => $dataMaterial['idMaterial']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function changeFlagMaterial($id_material, $flag)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE materials SET flag_indirect = :flag_indirect WHERE id_material = :id_material");
            $stmt->execute([
                'flag_indirect' => $flag,
                'id_material' => $id_material
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
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
