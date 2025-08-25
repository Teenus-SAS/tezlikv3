<?php

use tezlikv3\Dao\{
    ConversionUnitsDao,
    MaterialsDao,
    CostMaterialsDao,
    FilesDao,
    GeneralCategoriesDao,
    GeneralCompositeProductsDao,
    GeneralProductsDao,
    GeneralMaterialsDao,
    LastDataDao,
    ProductsMaterialsDao,
    MagnitudesDao,
    PriceProductDao,
    PriceUSDDao,
    TrmDao,
    UnitsDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

// Agrupar todas las rutas de materials bajo el prefijo '/materials'
$app->group('/materials', function (RouteCollectorProxy $group) {

    /* Consulta todos */
    $group->get('', function (Request $request, Response $response, $args) {
        $generalMaterialsDao = new GeneralMaterialsDao();

        // session_start();
        $id_company = $_SESSION['id_company'];
        $materials = $generalMaterialsDao->findAllMaterialsByCompany($id_company);
        $response->getBody()->write(json_encode($materials));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /* Consultar productos relacionados con la materia prima */
    $group->get('/productsByMaterials/{id_material}', function (Request $request, Response $response, $args) {
        $generalMaterialsDao = new GeneralMaterialsDao();

        $products = $generalMaterialsDao->findAllProductsByMaterials($args['id_material']);
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/addMaterials', function (Request $request, Response $response, $args) {

        $materialsDao = new MaterialsDao();
        $generalMaterialsDao = new GeneralMaterialsDao();
        $lastDataDao = new LastDataDao();
        $trmDao = new TrmDao();
        $costMaterialsDao = new CostMaterialsDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $generalProductsDao = new GeneralProductsDao();
        $generalCompositeProductsDao = new GeneralCompositeProductsDao();

        $dataMaterial = $request->getParsedBody();
        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $dataMaterials = sizeof($dataMaterial);

        $materialsBD = $generalMaterialsDao->findAllMaterialsByCompany($id_company);

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

                    if ($trm == 1) {
                        $resp = ['error' => true, 'message' => 'Error al guardar la información. Intente mas tarde'];

                        $response->getBody()->write(json_encode($resp));
                        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
                    }

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

                    if ($_SESSION['export_import'] == '1' && $_SESSION['flag_export_import'] == '1') {
                        $materials = $generalMaterialsDao->saveAllCostsUSDMaterial($dataMaterial);

                        $dataMaterial['costImport'] = floatval($dataMaterial['costImport']) * floatval($coverage_usd);
                        $dataMaterial['costExport'] = floatval($dataMaterial['costExport']) * floatval($coverage_usd);
                        $dataMaterial['costTotal'] = floatval($dataMaterial['costRawMaterial']) + floatval($dataMaterial['costImport']) + floatval($dataMaterial['costExport']);

                        $materials = $generalMaterialsDao->saveCostsMaterial($dataMaterial);
                    }
                } else {
                    $materials = $materialsDao->insertMaterialsByCompany($dataMaterial, $id_company);

                    if ($_SESSION['export_import'] == '1' && $_SESSION['flag_export_import'] == '1') {
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

            // Función para manejar inserciones
            function handleInsert($material, $id_company, $trmDao, $generalMaterialsDao, $lastDataDao, $materialsDao, &$response)
            {
                $material['usd'] = 1;
                $cost = str_replace(',', '.', $material['costRawMaterial']);
                $coverage_usd = $_SESSION['coverage_usd'];
                $trm = $trmDao->getLastTrm();

                if ($trm == 1) {
                    $resp = ['error' => true, 'message' => 'Error al guardar la información. Intente más tarde'];
                    $response->getBody()->write(json_encode($resp));
                    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
                }

                $trm = $trm[0]['valor'];
                $material['costRawMaterial'] = $cost * floatval($coverage_usd);
                $resolution = $materialsDao->insertMaterialsByCompany($material, $id_company);

                if ($resolution != null) return $resolution;

                $lastData = $lastDataDao->lastInsertedMaterialsId($id_company);
                $data = [
                    'date' => date('Y-m-d'),
                    'observation' => sprintf(
                        "Precio en Dolares: $%s. Valor del Dolar en la que se encuentra ahora: $%s. TRM Actual: $%s",
                        number_format($cost, 2, ',', '.'),
                        number_format($coverage_usd, 2, ',', '.'),
                        number_format($trm, 2, ',', '.')
                    ),
                    'idMaterial' => $lastData['id_material'],
                    'cost_usd' => $cost
                ];

                if ($generalMaterialsDao->saveBillMaterial($data) != null) return $resolution;
                if ($generalMaterialsDao->saveCostUSDMaterial($data) != null) return $resolution;

                if ($_SESSION['export_import'] == '1' && $_SESSION['flag_export_import'] == '1') {
                    $material['idMaterial'] = $lastData['id_material'];
                    $resolution = $generalMaterialsDao->saveAllCostsUSDMaterial($material);
                    if ($resolution != null) return $resolution;

                    $material['costImport'] = floatval($material['costImport']) * floatval($coverage_usd);
                    $material['costExport'] = floatval($material['costExport']) * floatval($coverage_usd);
                    $material['costTotal'] = floatval($material['costRawMaterial']) + floatval($material['costImport']) + floatval($material['costExport']);
                    $resolution = $generalMaterialsDao->saveCostsMaterial($material);
                }

                return $resolution;
            }

            // Función para manejar actualizaciones
            function handleUpdate($material, $id_company, $trmDao, $generalMaterialsDao, $materialsDao)
            {
                $material['usd'] = 1;
                $cost = str_replace(',', '.', $material['costRawMaterial']);
                $coverage_usd = $_SESSION['coverage_usd'];
                $trm = $trmDao->getLastTrm();

                if ($trm == 1) {
                    return ['error' => true, 'message' => 'Error al guardar la información. Intente más tarde'];
                }

                $trm = $trm[0]['valor'];
                $material['costRawMaterial'] = $cost * floatval($coverage_usd);
                $resolution = $materialsDao->updateMaterialsByCompany($material, $id_company);

                if ($resolution != null) return $resolution;

                $data = [
                    'date' => date('Y-m-d'),
                    'observation' => sprintf(
                        "Precio en Dolares: $%s. Valor del Dolar en la que se encuentra ahora: $%s. TRM Actual: $%s",
                        number_format($cost, 2, ',', '.'),
                        number_format($coverage_usd, 2, ',', '.'),
                        number_format($trm, 2, ',', '.')
                    ),
                    'idMaterial' => $material['idMaterial'],
                    'cost_usd' => $cost
                ];

                if ($generalMaterialsDao->saveBillMaterial($data) != null) return $resolution;
                if ($generalMaterialsDao->saveCostUSDMaterial($data) != null) return $resolution;

                if ($_SESSION['export_import'] == '1' && $_SESSION['flag_export_import'] == '1') {
                    $resolution = $generalMaterialsDao->saveAllCostsUSDMaterial($material);
                    if ($resolution != null) return $resolution;

                    $material['costImport'] = floatval($material['costImport']) * floatval($coverage_usd);
                    $material['costExport'] = floatval($material['costExport']) * floatval($coverage_usd);
                    $material['costTotal'] = floatval($material['costRawMaterial']) + floatval($material['costImport']) + floatval($material['costExport']);
                    $resolution = $generalMaterialsDao->saveCostsMaterial($material);
                }

                return $resolution;
            }

            function findAndAddMaterialIdOptimized(array $material, array $materialsBD): array
            {
                static $indexedMaterials = null;

                // Indexar los materiales solo una vez
                if ($indexedMaterials === null) {
                    $indexedMaterials = [];
                    foreach ($materialsBD as $item) {
                        $key = strtolower(trim($item['reference'] ?? '') . '|' . trim($item['material'] ?? ''));
                        $indexedMaterials[$key] = $item['id_material'] ?? null;
                    }
                }

                $searchKey = strtolower(trim($material['refRawMaterial'] ?? '') . '|' . trim($material['nameRawMaterial'] ?? ''));

                if (isset($indexedMaterials[$searchKey]))
                    $material['idMaterial'] = $indexedMaterials[$searchKey];

                return $material;
            }

            // Función principal optimizada
            foreach ($materials as $material) {

                //buscar si existe
                $material = findAndAddMaterialIdOptimized($material, $materialsBD);

                if (!isset($material['idMaterial'])) {
                    if ($material['typeCost'] == 'COP' || $_SESSION['flag_currency_usd'] == '0') {
                        $material['usd'] = 0;
                        $resolution = $materialsDao->insertMaterialsByCompany($material, $id_company);

                        if ($_SESSION['export_import'] == '1' && $_SESSION['flag_export_import'] == '1') {
                            $lastData = $lastDataDao->lastInsertedMaterialsId($id_company);
                            $material['idMaterial'] = $lastData['id_material'];
                            $material['costTotal'] = floatval($material['costRawMaterial']) + floatval($material['costImport']) + floatval($material['costExport']);
                            $resolution = $generalMaterialsDao->saveCostsMaterial($material);
                        }
                    } else {
                        $resolution = handleInsert($material, $id_company, $trmDao, $generalMaterialsDao, $lastDataDao, $materialsDao, $response);
                        if ($resolution != null) break;
                    }
                } else {
                    if ($material['typeCost'] == 'COP' || $_SESSION['flag_currency_usd'] == '0') {
                        $material['usd'] = 0;
                        $resolution = $materialsDao->updateMaterialsByCompany($material, $id_company);

                        if ($_SESSION['export_import'] == '1' && $_SESSION['flag_export_import'] == '1') {
                            $resolution = $generalMaterialsDao->saveAllCostsUSDMaterial($material);
                            if (!$material['costRawMaterial'] || !$material['costImport'] || !$material['costExport']) {
                                $material;
                            }

                            $material['costTotal'] = floatval($material['costRawMaterial']) + floatval($material['costImport']) + floatval($material['costExport']);
                            $resolution = $generalMaterialsDao->saveCostsMaterial($material);
                        }
                    } else {
                        $resolution = handleUpdate($material, $id_company, $trmDao, $generalMaterialsDao, $materialsDao);
                        if (isset($resolution['error'])) {
                            $response->getBody()->write(json_encode($resolution));
                            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
                        }
                    }
                }

                // Process products related to the material
                if ($resolution != null) break;
                $dataProducts = $costMaterialsDao->findProductByMaterial($material['idMaterial'], $id_company);
                foreach ($dataProducts as $arr) {
                    if ($arr['id_product'] != 0) {
                        $data = $priceProductDao->calcPrice($arr['id_product']);
                        if (isset($data['totalPrice'])) {
                            $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
                            if (isset($resolution['info'])) break;

                            if ($_SESSION['flag_currency_usd'] == '1') {
                                $k = ['price' => $data['totalPrice'], 'sale_price' => $data['sale_price'], 'id_product' => $arr['id_product']];
                                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                            }
                            if (isset($resolution['info'])) break;

                            if ($_SESSION['flag_composite_product'] == '1') {
                                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);
                                foreach ($productsCompositer as $j) {
                                    $data = ['compositeProduct' => $j['id_child_product'], 'idProduct' => $j['id_product']];
                                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                    $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);
                                    if (isset($resolution['info'])) break;

                                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                    $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);
                                    if (isset($resolution['info'])) break;

                                    $data = $priceProductDao->calcPrice($j['id_product']);
                                    if (isset($data['totalPrice'])) {
                                        $resolution = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);
                                        if (isset($resolution['info'])) break;

                                        if ($_SESSION['flag_currency_usd'] == '1') {
                                            $k = ['price' => $data['totalPrice'], 'sale_price' => $data['sale_price'], 'id_product' => $j['id_product']];
                                            $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                                        }
                                    }
                                    if (isset($resolution['info'])) break;
                                }
                            }
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

    $group->post('/updateMaterials', function (Request $request, Response $response, $args) {

        $materialsDao = new MaterialsDao();
        $generalMaterialsDao = new GeneralMaterialsDao();
        $trmDao = new TrmDao();
        $productMaterialsDao = new ProductsMaterialsDao();
        $costMaterialsDao = new CostMaterialsDao();
        $conversionUnitsDao = new ConversionUnitsDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $generalProductsDao = new GeneralProductsDao();
        $generalCompositeProductsDao = new GeneralCompositeProductsDao();

        // session_start();
        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $dataMaterial = $request->getParsedBody();

        $data = [];

        $material = $generalMaterialsDao->findMaterial($dataMaterial, $id_company);

        !is_array($material) ? $data['id_material'] = 0 : $data = $material;

        if ($data['id_material'] == $dataMaterial['idMaterial'] || $data['id_material'] == 0) {
            if ($dataMaterial['usd'] == '1' && $_SESSION['flag_currency_usd'] == '1') {
                $cost = $dataMaterial['costRawMaterial'];
                $formatCost = sprintf('$%s', number_format($cost, 2, ',', '.'));

                $coverage_usd = $_SESSION['coverage_usd'];
                $formatCoverageUsd = sprintf('$%s', number_format($coverage_usd, 2, ',', '.'));

                $trm = $trmDao->getLastTrm();

                if ($trm == 1) {
                    $resp = ['error' => true, 'message' => 'Error al guardar la información. Intente mas tarde'];

                    $response->getBody()->write(json_encode($resp));
                    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
                }

                $trm = $trm[0]['valor'];

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

                if ($_SESSION['export_import'] == '1' && $_SESSION['flag_export_import'] == '1') {
                    $materials = $generalMaterialsDao->saveAllCostsUSDMaterial($dataMaterial);

                    $dataMaterial['costImport'] = floatval($dataMaterial['costImport']) * floatval($coverage_usd);
                    $dataMaterial['costExport'] = floatval($dataMaterial['costExport']) * floatval($coverage_usd);
                    $dataMaterial['costTotal'] = floatval($dataMaterial['costRawMaterial']) + floatval($dataMaterial['costImport']) + floatval($dataMaterial['costExport']);

                    $materials = $generalMaterialsDao->saveCostsMaterial($dataMaterial);
                }
            } else {
                $materials = $materialsDao->updateMaterialsByCompany($dataMaterial, $id_company);

                if ($_SESSION['export_import'] == '1' && $_SESSION['flag_export_import'] == '1') {
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

                        if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                            $k = [];
                            $k['price'] = $data['totalPrice'];
                            $k['sale_price'] = $data['sale_price'];
                            $k['id_product'] = $j['id_product'];

                            $materials = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                        }

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
                                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                                    $k = [];
                                    $k['price'] = $data['totalPrice'];
                                    $k['sale_price'] = $data['sale_price'];
                                    $k['id_product'] = $arr['id_product'];

                                    $materials = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                                }

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

                                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
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
    })->add(new SessionMiddleware());

    $group->post('/saveBillMaterial', function (Request $request, Response $response, $args) {

        $id_company = $_SESSION['id_company'];
        $dataMaterial = $request->getParsedBody();

        $generalMaterialsDao = new GeneralMaterialsDao();
        $filesDao = new FilesDao();

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

    $group->post('/deleteMaterial', function (Request $request, Response $response, $args) {

        $generalMaterialsDao = new GeneralMaterialsDao();

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

    $group->get('/changeIndirect/{id_material}/{op}', function (Request $request, Response $response, $args) {
        $generalMaterialsDao = new GeneralMaterialsDao();

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
})->add(new SessionMiddleware());
