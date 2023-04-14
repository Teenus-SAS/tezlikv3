<?php

use tezlikv3\Dao\ConversionUnitsDao;
use tezlikv3\dao\MaterialsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\ProductsMaterialsDao;
use tezlikv3\Dao\MagnitudesDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\dao\UnitsDao;

$materialsDao = new MaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$productMaterialsDao = new ProductsMaterialsDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$costMaterialsDao = new CostMaterialsDao();
$priceProductDao = new PriceProductDao();
$GeneralProductsDao = new GeneralProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/materials', function (Request $request, Response $response, $args) use ($generalMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $generalMaterialsDao->findAllMaterialsByCompany($id_company);
    $response->getBody()->write(json_encode($materials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar Materias prima importada */
$app->post('/materialsDataValidation', function (Request $request, Response $response, $args) use (
    $generalMaterialsDao,
    $magnitudesDao,
    $unitsDao
) {
    $dataMaterial = $request->getParsedBody();

    if (isset($dataMaterial)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $materials = $dataMaterial['importMaterials'];

        for ($i = 0; $i < sizeof($materials); $i++) {
            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($materials[$i]);

            if (!$magnitude) {
                $i = $i + 1;
                $dataImportMaterial = array('error' => true, 'message' => "Magnitud no existe en la base de datos. Fila: $i");
                break;
            }

            $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($materials[$i]);

            if (!$unit) {
                $i = $i + 1;
                $dataImportMaterial = array('error' => true, 'message' => "Unidad no existe en la base de datos. Fila: $i");
                break;
            }

            if (
                empty($materials[$i]['refRawMaterial']) || empty($materials[$i]['nameRawMaterial']) || $materials[$i]['costRawMaterial'] == ''
            ) {
                $i = $i + 1;
                $dataImportMaterial = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            } else if ($materials[$i]['costRawMaterial'] == 0) {
                $i = $i + 1;
                $dataImportMaterial = array('error' => true, 'message' => "El costo debe ser mayor a cero (0), fila: $i");
                break;
            } else {
                $findMaterial = $generalMaterialsDao->findMaterial($materials[$i], $id_company);
                if (!$findMaterial) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportMaterial['insert'] = $insert;
                $dataImportMaterial['update'] = $update;
            }
        }
    } else
        $dataImportMaterial = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportMaterial, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao,
    $magnitudesDao,
    $unitsDao,
    $costMaterialsDao,
    $generalMaterialsDao,
    $priceProductDao,
    $GeneralProductsDao
) {
    session_start();
    $dataMaterial = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $dataMaterials = sizeof($dataMaterial);

    if ($dataMaterials > 1) {

        $material = $generalMaterialsDao->findMaterial($dataMaterial, $id_company);

        if (!$material) {
            $materials = $materialsDao->insertMaterialsByCompany($dataMaterial, $id_company);

            if ($materials == null)
                $resp = array('success' => true, 'message' => 'Materia Prima creada correctamente');
            else if (isset($materials['info']))
                $resp = array('info' => true, 'message' => $materials['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'La referencia ya existe. Ingrese una nueva referencia');
    } else {
        $materials = $dataMaterial['importMaterials'];

        for ($i = 0; $i < sizeof($materials); $i++) {

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($materials[$i]);
            $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($materials[$i]);
            $materials[$i]['unit'] = $unit['id_unit'];

            $materials[$i]['costRawMaterial'] = str_replace('.', ',', $materials[$i]['costRawMaterial']);

            $material = $generalMaterialsDao->findMaterial($materials[$i], $id_company);

            if (!$material)
                $resolution = $materialsDao->insertMaterialsByCompany($materials[$i], $id_company);
            else {
                $materials[$i]['idMaterial'] = $material['id_material'];
                $resolution = $materialsDao->updateMaterialsByCompany($materials[$i], $id_company);

                if ($resolution != null) break;

                $dataProducts = $costMaterialsDao->findProductByMaterial($materials[$i]['idMaterial'], $id_company);

                foreach ($dataProducts as $arr) {
                    if ($arr['id_product'] != 0) {
                        $resolution = $priceProductDao->calcPrice($arr['id_product']);

                        if (isset($resolution['info'])) break;

                        $resolution = $GeneralProductsDao->updatePrice($arr['id_product'], $resolution['totalPrice']);
                    }
                }
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia Prima Importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao,
    $generalMaterialsDao,
    $productMaterialsDao,
    $costMaterialsDao,
    $conversionUnitsDao,
    $priceProductDao,
    $GeneralProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMaterial = $request->getParsedBody();

    $material = $generalMaterialsDao->findMaterial($dataMaterial, $id_company);

    !isset($material['id_material']) ? $material['id_material'] = 0 : $material;

    if ($material['id_material'] == $dataMaterial['idMaterial'] || $material['id_material'] == 0) {
        $materials = $materialsDao->updateMaterialsByCompany($dataMaterial, $id_company);

        if ($materials == null) {
            $dataProducts = $costMaterialsDao->findProductByMaterial($dataMaterial['idMaterial'], $id_company);

            foreach ($dataProducts as $j) {
                if ($j['id_product'] != 0) {
                    // Calcular precio total materias
                    // Consultar todos los datos del producto
                    $productsMaterial = $productMaterialsDao->findAllProductsmaterials($j['id_product'], $id_company);

                    $totalQuantity = 0;

                    foreach ($productsMaterial as $k) {
                        // Obtener materia prima
                        $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                        // Convertir unidades
                        $quantities = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                        !$quantities ? $quantities = 0 : $quantities;

                        $totalQuantity += $quantities;

                        // Convertir una unidad
                        // $quantity = $conversionUnitsDao->convertUnits($material, $k, 1);

                        // Modificar costo
                        $generalMaterialsDao->updateCostProductMaterial($k, $quantities);
                    }
                    $j['idProduct'] = $j['id_product'];
                    $j = $costMaterialsDao->calcCostMaterial($j, $totalQuantity, $id_company);

                    $materials = $costMaterialsDao->updateCostMaterials($j, $id_company);

                    // Calcular precio
                    $materials = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($materials['info'])) break;

                    $materials = $GeneralProductsDao->updatePrice($j['id_product'], $materials['totalPrice']);
                }
            }
        }

        if ($materials == null)
            $resp = array('success' => true, 'message' => 'Materia Prima actualizada correctamente');
        else if (isset($materials['info']))
            $resp = array('info' => true, 'message' => $materials['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'La referencia ya existe. Ingrese una nueva referencia');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteMaterial', function (Request $request, Response $response, $args) use ($generalMaterialsDao) {
    $dataMaterial = $request->getParsedBody();

    $materials = $generalMaterialsDao->deleteMaterial($dataMaterial['idMaterial']);

    if ($materials == null)
        $resp = array('success' => true, 'message' => 'Material eliminado correctamente');
    else if (isset($materials['info']))
        $resp = array('info' => true, 'message' => $materials['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el material');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
