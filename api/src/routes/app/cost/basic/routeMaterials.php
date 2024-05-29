<?php

use tezlikv3\Dao\ConversionUnitsDao;
use tezlikv3\dao\MaterialsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\FilesDao;
use tezlikv3\dao\GeneralCategoriesDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\ProductsMaterialsDao;
use tezlikv3\Dao\MagnitudesDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\Dao\TrmDao;
use tezlikv3\dao\UnitsDao;
use tezlikv3\dao\WebTokenDao;

$materialsDao = new MaterialsDao();
$webTokenDao = new WebTokenDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$generalCategoriesDao = new GeneralCategoriesDao();
$productMaterialsDao = new ProductsMaterialsDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$costMaterialsDao = new CostMaterialsDao();
$priceProductDao = new PriceProductDao();
$pricesUSDDao = new PriceUSDDao();
$generalProductsDao = new GeneralProductsDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$filesDao = new FilesDao();
$lastDataDao = new LastDataDao();
$trmDao = new TrmDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/materials', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalMaterialsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $generalMaterialsDao->findAllMaterialsByCompany($id_company);
    $response->getBody()->write(json_encode($materials));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos relacionados con la materia prima */
$app->get('/productsByMaterials/{id_material}', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalMaterialsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $products = $generalMaterialsDao->findAllProductsByMaterials($args['id_material']);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar Materias prima importada */
$app->post('/materialsDataValidation', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalMaterialsDao,
    $generalCategoriesDao,
    $magnitudesDao,
    $unitsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $dataMaterial = $request->getParsedBody();

    if (isset($dataMaterial)) {
        // session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $materials = $dataMaterial['importMaterials'];

        // Verificar duplicados
        $duplicateTracker = [];
        $dataImportMaterial = [];

        for ($i = 0; $i < count($materials); $i++) {
            if ($_SESSION['flag_currency_usd'] == '0') {
                if ($_SESSION['export_import'] == '1') {
                    if (
                        empty($materials[$i]['refRawMaterial']) || empty($materials[$i]['nameRawMaterial']) || $materials[$i]['costRawMaterial'] == '' ||
                        empty($materials[$i]['magnitude']) || empty($materials[$i]['unit']) || $materials[$i]['costImport'] == '' || $materials[$i]['costExport'] == ''
                    ) {
                        $i = $i + 2;
                        $dataImportMaterial = array('error' => true, 'message' => "Campos vacios, fila: $i");
                        break;
                    }
                    if (
                        empty(trim($materials[$i]['refRawMaterial'])) || empty(trim($materials[$i]['nameRawMaterial'])) || trim($materials[$i]['costRawMaterial']) == '' || trim($materials[$i]['costImport']) == '' ||
                        trim($materials[$i]['costExport']) == '' || empty(trim($materials[$i]['magnitude'])) || empty(trim($materials[$i]['unit']))
                    ) {
                        $i = $i + 2;
                        $dataImportMaterial = array('error' => true, 'message' => "Campos vacios, fila: $i");
                        break;
                    }
                } else {
                    if (
                        empty($materials[$i]['refRawMaterial']) || empty($materials[$i]['nameRawMaterial']) || $materials[$i]['costRawMaterial'] == '' ||
                        empty($materials[$i]['magnitude']) || empty($materials[$i]['unit'])
                    ) {
                        $i = $i + 2;
                        $dataImportMaterial = array('error' => true, 'message' => "Campos vacios, fila: $i");
                        break;
                    }
                    if (
                        empty(trim($materials[$i]['refRawMaterial'])) || empty(trim($materials[$i]['nameRawMaterial'])) || trim($materials[$i]['costRawMaterial']) == '' ||
                        empty(trim($materials[$i]['magnitude'])) || empty(trim($materials[$i]['unit']))
                    ) {
                        $i = $i + 2;
                        $dataImportMaterial = array('error' => true, 'message' => "Campos vacios, fila: $i");
                        break;
                    }
                }
            } else {
                if (
                    empty($materials[$i]['refRawMaterial']) || empty($materials[$i]['nameRawMaterial']) || $materials[$i]['costRawMaterial'] == '' ||
                    empty($materials[$i]['magnitude']) || empty($materials[$i]['unit']) || empty($materials[$i]['typeCost'])
                ) {
                    $i = $i + 2;
                    $dataImportMaterial = array('error' => true, 'message' => "Campos vacios, fila: $i");
                    break;
                }
                if (
                    empty(trim($materials[$i]['refRawMaterial'])) || empty(trim($materials[$i]['nameRawMaterial'])) || trim($materials[$i]['costRawMaterial']) == '' ||
                    empty(trim($materials[$i]['magnitude'])) || empty(trim($materials[$i]['unit'])) || empty(trim($materials[$i]['typeCost']))
                ) {
                    $i = $i + 2;
                    $dataImportMaterial = array('error' => true, 'message' => "Campos vacios, fila: $i");
                    break;
                }
            }

            // Categorias
            // $findCategory = $generalCategoriesDao->findCategory($materials[$i], $id_company);

            // if (!$findCategory) {
            //     $i = $i + 2;
            //     $dataImportMaterial =  array('error' => true, 'message' => "Categoria no exsite en la base de datos. Fila : $i");
            //     break;
            // }

            $item = $materials[$i];
            $refRawMaterial = trim($item['refRawMaterial']);
            $nameRawMaterial = trim($item['nameRawMaterial']);

            if (isset($duplicateTracker[$refRawMaterial]) || isset($duplicateTracker[$nameRawMaterial])) {
                $i = $i + 2;
                $dataImportMaterial =  array('error' => true, 'message' => "Duplicación encontrada en la fila: $i.<br>- Referencia: $refRawMaterial<br>- Material: $nameRawMaterial");
                break;
            } else {
                $duplicateTracker[$refRawMaterial] = true;
                $duplicateTracker[$nameRawMaterial] = true;
            }
        }

        if (sizeof($dataImportMaterial) == 0) {
            for ($i = 0; $i < sizeof($materials); $i++) {
                $materials[$i]['costRawMaterial'] = str_replace(',', '.', $materials[$i]['costRawMaterial']);

                if (floatval($materials[$i]['costRawMaterial']) == false) {
                    $i = $i + 2;
                    $dataImportMaterial = array('error' => true, 'message' => "El costo debe ser mayor a cero (0), fila: $i");
                    break;
                }

                $cost = 1 * $materials[$i]['costRawMaterial'];

                if ($cost <= 0 || is_nan($cost)) {
                    $i = $i + 2;
                    $dataImportMaterial = array('error' => true, 'message' => "El costo debe ser mayor a cero (0), fila: $i");
                    break;
                }

                // Consultar magnitud
                $magnitude = $magnitudesDao->findMagnitude($materials[$i]);

                if (!$magnitude) {
                    $i = $i + 2;
                    $dataImportMaterial = array('error' => true, 'message' => "Magnitud no existe en la base de datos. Fila: $i");
                    break;
                }

                $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

                // Consultar unidad
                $unit = $unitsDao->findUnit($materials[$i]);

                if (!$unit) {
                    $i = $i + 2;
                    $dataImportMaterial = array('error' => true, 'message' => "Unidad no existe en la base de datos. Fila: $i");
                    break;
                }

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
    $webTokenDao,
    $materialsDao,
    $generalMaterialsDao,
    $lastDataDao,
    $trmDao,
    $generalCategoriesDao,
    $magnitudesDao,
    $unitsDao,
    $costMaterialsDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao,
    $generalCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $dataMaterial = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataMaterials = sizeof($dataMaterial);

    if ($dataMaterials > 1) {

        $material = $generalMaterialsDao->findMaterialByReferenceOrName($dataMaterial, $id_company);

        if (!$material) {
            if ($dataMaterial['usd'] == '1' && $_SESSION['flag_currency_usd'] == '1') {
                $cost = $dataMaterial['costRawMaterial'];
                $formatCost = sprintf('$%s', number_format($cost, 2, ',', '.'));

                $coverage_usd = $_SESSION['coverage_usd'];
                $formatCoverageUsd = sprintf('$%s', number_format($coverage_usd, 2, ',', '.'));

                // if ($trm == 0) {
                $trm = $trmDao->getLastTrm();
                $trm = $trm[0]['valor'];
                // }

                $dataMaterial['costRawMaterial'] = $cost * floatval($coverage_usd);

                $materials = $materialsDao->insertMaterialsByCompany($dataMaterial, $id_company);

                $formatTrm = sprintf('$%s', number_format($trm, 2, ',', '.'));

                $data = [];
                $data['date'] = date('Y-m-d');
                $data['observation'] = "Precio en Dolares: $formatCost. Valor del Dolar en la que se encuentra ahora: $formatCoverageUsd. TRM Actual: $formatTrm";

                $lastData = $lastDataDao->lastInsertedMaterialsId($id_company);
                $data['idMaterial'] = $lastData['id_material'];
                $data['cost_usd'] = $cost;

                $materials = $generalMaterialsDao->saveBillMaterial($data);
                $materials = $generalMaterialsDao->saveCostUSDMaterial($data);

                if ($_SESSION['export_import'] == '1') {
                    $materials = $generalMaterialsDao->saveAllCostsUSDMaterial($dataMaterial);

                    $dataMaterial['costImport'] = floatval($dataMaterial['costImport']) * floatval($coverage_usd);
                    $dataMaterial['costExport'] = floatval($dataMaterial['costExport']) * floatval($coverage_usd);
                    $dataMaterial['costTotal'] = floatval($dataMaterial['costRawMaterial']) + floatval($dataMaterial['costImport']) + floatval($dataMaterial['costExport']);

                    $materials = $generalMaterialsDao->saveCostsMaterial($dataMaterial);
                }
            } else {
                $materials = $materialsDao->insertMaterialsByCompany($dataMaterial, $id_company);

                if ($_SESSION['export_import'] == '1') {
                    $lastData = $lastDataDao->lastInsertedMaterialsId($id_company);
                    $dataMaterial['idMaterial'] = $lastData['id_material'];

                    $dataMaterial['costTotal'] = floatval($dataMaterial['costRawMaterial']) + floatval($dataMaterial['costImport']) + floatval($dataMaterial['costExport']);

                    $materials = $generalMaterialsDao->saveCostsMaterial($dataMaterial);
                }
            }


            if ($materials == null)
                $resp = array('success' => true, 'message' => 'Materia Prima creada correctamente');
            else if (isset($materials['info']))
                $resp = array('info' => true, 'message' => $materials['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'La materia prima ya existe. Ingrese una nueva');
    } else {
        $materials = $dataMaterial['importMaterials'];

        for ($i = 0; $i < sizeof($materials); $i++) {
            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($materials[$i]);
            $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($materials[$i]);
            $materials[$i]['unit'] = $unit['id_unit'];

            empty($materials[$i]['category']) ? $materials[$i]['category'] = '' : $materials[$i]['category'];

            $findCategory = $generalCategoriesDao->findCategory($materials[$i], $id_company);

            if (!$findCategory)
                $materials[$i]['idCategory'] = 0;
            else
                $materials[$i]['idCategory'] = $findCategory['id_category'];

            $material = $generalMaterialsDao->findMaterial($materials[$i], $id_company);

            if (!$material) {
                if ($materials[$i]['typeCost'] == 'COP' || $_SESSION['flag_currency_usd'] == '0') {
                    $materials[$i]['usd'] = 0;
                    $resolution = $materialsDao->insertMaterialsByCompany($materials[$i], $id_company);

                    if ($_SESSION['export_import'] == '1') {
                        $lastData = $lastDataDao->lastInsertedMaterialsId($id_company);
                        $materials[$i]['idMaterial'] = $lastData['id_material'];

                        $materials[$i]['costTotal'] = floatval($materials[$i]['costRawMaterial']) + floatval($materials[$i]['costImport']) + floatval($materials[$i]['costExport']);

                        $resolution = $generalMaterialsDao->saveCostsMaterial($materials[$i]);
                    }
                } else {
                    $materials[$i]['usd'] = 1;

                    $cost = str_replace(',', '.', $materials[$i]['costRawMaterial']);
                    $formatCost = sprintf('$%s', number_format($cost, 2, ',', '.'));

                    $coverage_usd = $_SESSION['coverage_usd'];
                    $formatCoverageUsd = sprintf('$%s', number_format($coverage_usd, 2, ',', '.'));

                    // if ($trm == 0) {
                    $trm = $trmDao->getLastTrm();
                    $trm = $trm[0]['valor'];
                    // }

                    $materials[$i]['costRawMaterial'] = $cost * floatval($coverage_usd);

                    $resolution = $materialsDao->insertMaterialsByCompany($materials[$i], $id_company);
                    if ($resolution != null) break;

                    $formatTrm = sprintf('$%s', number_format($trm, 2, ',', '.'));

                    $data = [];
                    $data['date'] = date('Y-m-d');
                    $data['observation'] = "Precio en Dolares: $formatCost. Valor del Dolar en la que se encuentra ahora: $formatCoverageUsd. TRM Actual: $formatTrm";

                    $lastData = $lastDataDao->lastInsertedMaterialsId($id_company);
                    $data['idMaterial'] = $lastData['id_material'];
                    $data['cost_usd'] = $cost;

                    $resolution = $generalMaterialsDao->saveBillMaterial($data);
                    if ($resolution != null) break;
                    $resolution = $generalMaterialsDao->saveCostUSDMaterial($data);
                    if ($resolution != null) break;

                    if ($_SESSION['export_import'] == '1') {
                        $materials[$i]['idMaterial'] = $lastData['id_material'];
                        $resolution = $generalMaterialsDao->saveAllCostsUSDMaterial($materials[$i]);
                        if ($resolution != null) break;

                        $materials[$i]['costImport'] = floatval($materials[$i]['costImport']) * floatval($coverage_usd);
                        $dataMaterial['costExport'] = floatval($materials[$i]['costExport']) * floatval($coverage_usd);
                        $materials[$i]['costTotal'] = floatval($materials[$i]['costRawMaterial']) + floatval($materials[$i]['costImport']) + floatval($materials[$i]['costExport']);

                        $resolution = $generalMaterialsDao->saveCostsMaterial($materials[$i]);
                        if ($resolution != null) break;
                    }
                }
            } else {
                $materials[$i]['idMaterial'] = $material['id_material'];

                if ($materials[$i]['typeCost'] == 'COP' || $_SESSION['flag_currency_usd'] == '0') {
                    $materials[$i]['usd'] = 0;
                    $resolution = $materialsDao->updateMaterialsByCompany($materials[$i], $id_company);

                    if ($_SESSION['export_import'] == '1') {
                        $resolution = $generalMaterialsDao->saveAllCostsUSDMaterial($materials[$i]);

                        if (!$materials[$i]['costRawMaterial'] || !$materials[$i]['costImport'] || !$materials[$i]['costExport']) {
                            $materials[$i];
                        }

                        $materials[$i]['costTotal'] = floatval($materials[$i]['costRawMaterial']) + floatval($materials[$i]['costImport']) + floatval($materials[$i]['costExport']);

                        $resolution = $generalMaterialsDao->saveCostsMaterial($materials[$i]);
                    }
                } else {
                    $materials[$i]['usd'] = 1;

                    $cost = str_replace(',', '.', $materials[$i]['costRawMaterial']);

                    $formatCost = sprintf('$%s', number_format($cost, 2, ',', '.'));

                    $coverage_usd = $_SESSION['coverage_usd'];
                    $formatCoverageUsd = sprintf('$%s', number_format($coverage_usd, 2, ',', '.'));

                    // if ($trm == 0) {
                    $trm = $trmDao->getLastTrm();
                    $trm = $trm[0]['valor'];
                    // }

                    $materials[$i]['costRawMaterial'] = $cost * floatval($coverage_usd);
                    $resolution = $materialsDao->updateMaterialsByCompany($materials[$i], $id_company);
                    if ($resolution != null) break;

                    $formatTrm = sprintf('$%s', number_format($trm, 2, ',', '.'));

                    $data = [];
                    $data['date'] = date('Y-m-d');
                    $data['observation'] = "Precio en Dolares: $formatCost. Valor del Dolar en la que se encuentra ahora: $formatCoverageUsd. TRM Actual: $formatTrm";
                    $data['idMaterial'] = $materials[$i]['idMaterial'];
                    $data['cost_usd'] = $cost;

                    $resolution = $generalMaterialsDao->saveBillMaterial($data);
                    if ($resolution != null) break;
                    $resolution = $generalMaterialsDao->saveCostUSDMaterial($data);
                    if ($resolution != null) break;

                    if ($_SESSION['export_import'] == '1') {
                        $resolution = $generalMaterialsDao->saveAllCostsUSDMaterial($materials[$i]);
                        if ($resolution != null) break;

                        $materials[$i]['costImport'] = floatval($materials[$i]['costImport']) * floatval($coverage_usd);
                        $materials[$i]['costExport'] = floatval($materials[$i]['costExport']) * floatval($coverage_usd);
                        $materials[$i]['costTotal'] = floatval($materials[$i]['costRawMaterial']) + floatval($materials[$i]['costImport']) + floatval($materials[$i]['costExport']);

                        $resolution = $generalMaterialsDao->saveCostsMaterial($materials[$i]);
                        if ($resolution != null) break;
                    }
                }

                if ($resolution != null) break;

                // if (!$materials[$i]['idMaterial']) {
                //     $materials[$i];
                // }
                $dataProducts = $costMaterialsDao->findProductByMaterial($materials[$i]['idMaterial'], $id_company);

                foreach ($dataProducts as $arr) {
                    if ($arr['id_product'] != 0) {
                        $data = $priceProductDao->calcPrice($arr['id_product']);

                        if (isset($data['totalPrice']))
                            $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                        if (isset($resolution['info'])) break;

                        // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $arr['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

                        if (isset($resolution['info'])) break;

                        if ($_SESSION['flag_composite_product'] == '1') {
                            if (isset($resolution['info'])) break;
                            // Calcular costo material porq
                            $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                            foreach ($productsCompositer as $j) {
                                if (isset($resolution['info'])) break;

                                $data = [];
                                $data['compositeProduct'] = $j['id_child_product'];
                                $data['idProduct'] = $j['id_product'];
                                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                                if (isset($resolution['info'])) break;
                                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                                if (isset($resolution['info'])) break;

                                $data = $priceProductDao->calcPrice($j['id_product']);
                                if (isset($data['totalPrice']))
                                    $resolution = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                                if (isset($resolution['info'])) break;
                                // Convertir a Dolares 
                                $k = [];
                                $k['price'] = $data['totalPrice'];
                                $k['sale_price'] = $data['sale_price'];
                                $k['id_product'] = $j['id_product'];

                                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                                if (isset($resolution['info'])) break;

                                $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                                foreach ($productsCompositer2 as $k) {
                                    if (isset($resolution['info'])) break;

                                    $data = [];
                                    $data['compositeProduct'] = $k['id_child_product'];
                                    $data['idProduct'] = $k['id_product'];

                                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                    $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                                    if (isset($resolution['info'])) break;
                                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                    $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                                    if (isset($resolution['info'])) break;

                                    $data = $priceProductDao->calcPrice($k['id_product']);

                                    if (isset($data['totalPrice']))
                                        $resolution = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);

                                    if (isset($resolution['info'])) break;
                                    // Convertir a Dolares 
                                    $l = [];
                                    $l['price'] = $data['totalPrice'];
                                    $l['sale_price'] = $data['sale_price'];
                                    $l['id_product'] = $k['id_product'];

                                    $resolution = $pricesUSDDao->calcPriceUSDandModify($l, $coverage_usd);
                                }
                            }
                        }
                    }
                }
            }

            // if ($_SESSION['export_import'] == '1') {
            //     $materials[$i]['costTotal'] = $materials[$i]['costRawMaterial'] + $materials[$i]['costImport'] + $materials[$i]['costExport'];

            //     $material = $generalMaterialsDao->saveCostsMaterial($materials[$i]);
            // }
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
    $webTokenDao,
    $materialsDao,
    $generalMaterialsDao,
    $trmDao,
    $productMaterialsDao,
    $costMaterialsDao,
    $conversionUnitsDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao,
    $generalCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataMaterial = $request->getParsedBody();

    $data = [];
    $status = true;

    $materials = $generalMaterialsDao->findMaterialByReferenceOrName($dataMaterial, $id_company);

    foreach ($materials as $arr) {
        if ($arr['id_material'] != $dataMaterial['idMaterial']) {
            $status = false;
            break;
        }
    }

    if ($status == true) {
        if ($dataMaterial['usd'] == '1' && $_SESSION['flag_currency_usd'] == '1') {
            $cost = $dataMaterial['costRawMaterial'];
            $formatCost = sprintf('$%s', number_format($cost, 2, ',', '.'));

            $coverage_usd = $_SESSION['coverage_usd'];
            $formatCoverageUsd = sprintf('$%s', number_format($coverage_usd, 2, ',', '.'));

            // if ($trm == 0) {
            $trm = $trmDao->getLastTrm();
            $trm = $trm[0]['valor'];
            // } 

            $dataMaterial['costRawMaterial'] = $cost * floatval($coverage_usd);
            $materials = $materialsDao->updateMaterialsByCompany($dataMaterial, $id_company);

            $formatTrm = sprintf('$%s', number_format($trm, 2, ',', '.'));

            $data = [];
            $data['date'] = date('Y-m-d');
            $data['observation'] = "Precio en Dolares: $formatCost. Valor del Dolar en la que se encuentra ahora: $formatCoverageUsd. TRM Actual: $formatTrm";
            $data['idMaterial'] = $dataMaterial['idMaterial'];
            $data['cost_usd'] = $cost;

            $materials = $generalMaterialsDao->saveBillMaterial($data);
            $materials = $generalMaterialsDao->saveCostUSDMaterial($data);

            if ($_SESSION['export_import'] == '1') {
                $materials = $generalMaterialsDao->saveAllCostsUSDMaterial($dataMaterial);

                $dataMaterial['costImport'] = floatval($dataMaterial['costImport']) * floatval($coverage_usd);
                $dataMaterial['costExport'] = floatval($dataMaterial['costExport']) * floatval($coverage_usd);
                $dataMaterial['costTotal'] = floatval($dataMaterial['costRawMaterial']) + floatval($dataMaterial['costImport']) + floatval($dataMaterial['costExport']);

                $materials = $generalMaterialsDao->saveCostsMaterial($dataMaterial);
            }
        } else {
            $materials = $materialsDao->updateMaterialsByCompany($dataMaterial, $id_company);

            if ($_SESSION['export_import']) {
                $dataMaterial['costTotal'] = floatval($dataMaterial['costRawMaterial']) + floatval($dataMaterial['costImport']) + floatval($dataMaterial['costExport']);

                $materials = $generalMaterialsDao->saveCostsMaterial($dataMaterial);
            }
        }

        if ($materials == null) {
            $dataProducts = $costMaterialsDao->findProductByMaterial($dataMaterial['idMaterial'], $id_company);

            foreach ($dataProducts as $j) {
                if ($j['id_product'] != 0) {
                    if (isset($materials['info'])) break;

                    // Calcular precio total materias
                    // Consultar todos los datos del producto
                    $productsMaterial = $productMaterialsDao->findAllProductsmaterialsByIdProduct($j['id_product'], $id_company);

                    foreach ($productsMaterial as $k) {
                        // Obtener materia prima
                        $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                        // Convertir unidades
                        $quantities = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                        // Modificar costo
                        $generalMaterialsDao->updateCostProductMaterial($k, $quantities);
                    }
                    $j['idProduct'] = $j['id_product'];
                    $status = false;

                    if ($_SESSION['flag_composite_product'] == '1') {
                        $composite = $generalCompositeProductsDao->findCompositeProductCost($j['idProduct']);

                        !$composite ? $status = false : $status = true;

                        if ($status == true)
                            $dataMaterial = $costMaterialsDao->calcCostMaterialByCompositeProduct($j, $id_company);
                    }

                    if ($_SESSION['flag_composite_product'] == '0' || $status == false)
                        $dataMaterial = $costMaterialsDao->calcCostMaterial($j, $id_company);

                    $materials = $costMaterialsDao->updateCostMaterials($dataMaterial, $id_company);

                    // Calcular precio
                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $materials = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($materials['info'])) break;

                    // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $data['totalPrice'];
                    $k['sale_price'] = $data['sale_price'];
                    $k['id_product'] = $j['id_product'];

                    $materials = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

                    if (isset($materials['info'])) break;

                    if ($_SESSION['flag_composite_product'] == '1') {
                        if (isset($materials['info'])) break;
                        // Calcular costo material porq
                        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                        foreach ($productsCompositer as $arr) {
                            if (isset($materials['info'])) break;

                            $data = [];
                            $data['compositeProduct'] = $arr['id_child_product'];
                            $data['idProduct'] = $arr['id_product'];
                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $materials = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($materials['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $materials = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($materials['info'])) break;

                            $data = $priceProductDao->calcPrice($arr['id_product']);
                            if (isset($data['totalPrice']))
                                $materials = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                            if (isset($materials['info'])) break;
                            // Convertir a Dolares 
                            $k = [];
                            $k['price'] = $data['totalPrice'];
                            $k['sale_price'] = $data['sale_price'];
                            $k['id_product'] = $arr['id_product'];

                            $materials = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

                            if (isset($materials['info'])) break;

                            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                            foreach ($productsCompositer2 as $k) {
                                if (isset($materials['info'])) break;

                                $data = [];
                                $data['compositeProduct'] = $k['id_child_product'];
                                $data['idProduct'] = $k['id_product'];

                                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                $materials = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                                if (isset($materials['info'])) break;
                                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                $materials = $costMaterialsDao->updateCostMaterials($data, $id_company);

                                if (isset($materials['info'])) break;

                                $data = $priceProductDao->calcPrice($k['id_product']);
                                if (isset($data['totalPrice']))
                                    $materials = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);

                                // Convertir a Dolares 
                                $l = [];
                                $l['price'] = $data['totalPrice'];
                                $l['sale_price'] = $data['sale_price'];
                                $l['id_product'] = $arr['id_product'];

                                $materials = $pricesUSDDao->calcPriceUSDandModify($l, $coverage_usd);
                            }
                        }
                    }
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
        $resp = array('info' => true, 'message' => 'La materia prima ya existe. Ingrese una nueva');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/saveBillMaterial', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalMaterialsDao,
    $filesDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $dataMaterial = $request->getParsedBody();

    $materials = $generalMaterialsDao->saveBillMaterial($dataMaterial);
    if (sizeof($_FILES) > 0)
        $materials = $filesDao->imageMaterial($dataMaterial['idMaterial'], $id_company);

    if ($materials == null)
        $resp = array('success' => true, 'message' => 'Material modificado correctamente');
    else if (isset($materials['info']))
        $resp = array('info' => true, 'message' => $materials['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteMaterial', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalMaterialsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

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

$app->get('/changeIndirect/{id_material}/{op}', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalMaterialsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $status = true;

    if ($status == true) {
        $material = $generalMaterialsDao->changeFlagMaterial($args['id_material'], $args['op']);

        if ($material == null)
            $resp = array('success' => true, 'message' => 'Material modificado correctamente');
        else if (isset($material['info']))
            $resp = array('info' => true, 'message' => $material['message']);
        else
            $resp = array('error' => true, 'message' => 'No se pudo modificar la información. Intente de nuevo');
    } else
        $resp = array('error' => true, 'message' => 'No se pudo desactivar el material. Tiene datos relacionados a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
