<?php

use tezlikv3\dao\CompositeProductsDao;
use tezlikv3\Dao\ConversionUnitsDao;
use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\GeneralProductMaterialsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\ProductsMaterialsDao;
use tezlikv3\Dao\MagnitudesDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\UnitsDao;
use tezlikv3\dao\WebTokenDao;

$productsMaterialsDao = new ProductsMaterialsDao();
$webTokenDao = new WebTokenDao();
$generalProductMaterialsDao = new GeneralProductMaterialsDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new GeneralProductsDao();
$materialsDao = new GeneralMaterialsDao();
$costMaterialsDao = new CostMaterialsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$priceProductDao = new PriceProductDao();
$pricesUSDDao = new PriceUSDDao();
$compositeProductsDao = new CompositeProductsDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$indirectCostDao = new IndirectCostDao();
$costWorkforceDao = new CostWorkforceDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productsMaterials/{idProduct}', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $productsMaterialsDao
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

    $productMaterials = $productsMaterialsDao->findAllProductsmaterialsByIdProduct($args['idProduct'], $id_company);

    $response->getBody()->write(json_encode($productMaterials));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/allProductsMaterials', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalProductMaterialsDao
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

    $productMaterials = $generalProductMaterialsDao->findAllProductsmaterials($id_company);

    $response->getBody()->write(json_encode($productMaterials));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/productsMaterialsBasic', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalProductMaterialsDao
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

    $productMaterials = $generalProductMaterialsDao->findDataBasicProductsMaterials($id_company);

    $response->getBody()->write(json_encode($productMaterials));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/productsMaterialsDataValidation', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $productsMaterialsDao,
    $magnitudesDao,
    $unitsDao,
    $productsDao,
    $materialsDao,
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

    $dataProductMaterial = $request->getParsedBody();

    if (isset($dataProductMaterial)) {
        // session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $productMaterials = $dataProductMaterial['importProductsMaterials'];

        for ($i = 0; $i < sizeof($productMaterials); $i++) {

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($productMaterials[$i]);

            if (!$magnitude) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Magnitud no existe en la base de datos. Fila: $i");
                break;
            }

            $productMaterials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($productMaterials[$i]);

            if (!$unit) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Unidad no existe en la base de datos. Fila: $i");
                break;
            }

            // if (
            //     empty($productMaterials[$i]['referenceProduct']) || empty($productMaterials[$i]['product']) || empty($productMaterials[$i]['refRawMaterial']) || empty($productMaterials[$i]['nameRawMaterial']) ||
            //     $productMaterials[$i]['quantity'] == '' || $productMaterials[$i]['waste'] == ''
            //     || empty($productMaterials[$i]['type'])
            // ) {
            //     $i = $i + 2;
            //     $dataImportProductsMaterials = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
            //     break;
            // }
            // if (
            //     empty(trim($productMaterials[$i]['referenceProduct'])) || empty(trim($productMaterials[$i]['product'])) || empty(trim($productMaterials[$i]['refRawMaterial'])) || empty(trim($productMaterials[$i]['nameRawMaterial'])) ||
            //     trim($productMaterials[$i]['quantity']) == '' || trim($productMaterials[$i]['waste']) == ''
            //     || empty(trim($productMaterials[$i]['type']))
            // ) {
            //     $i = $i + 2;
            //     $dataImportProductsMaterials = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
            //     break;
            // }

            // $quantity = str_replace(',', '.', $productMaterials[$i]['quantity']);

            // $quantity = 1 * $quantity;

            // if ($quantity <= 0 || is_nan($quantity)) {
            //     $i = $i + 2;
            //     $dataImportProductsMaterials = array('error' => true, 'message' => "La cantidad debe ser mayor a cero (0)<br>Fila: {$i}");
            //     break;
            // }

            // // Obtener id producto
            // $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            // if (!$findProduct) {
            //     $i = $i + 2;
            //     $dataImportProductsMaterials = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
            //     break;
            // } else $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            $type = strtoupper(trim($productMaterials[$i]['type']));

            if ($type == 'MATERIAL') {
                // Obtener id materia prima
                // $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
                // if (!$findMaterial) {
                //     $i = $i + 2;
                //     $dataImportProductsMaterials = array('error' => true, 'message' => "Materia prima no existe en la base de datos<br>Fila: {$i}");
                //     break;
                // } else $productMaterials[$i]['material'] = $findMaterial['id_material'];

                $findProductsMaterials = $productsMaterialsDao->findProductMaterial($productMaterials[$i], $id_company);
            } else {
                // $data = [];
                // $data['referenceProduct'] = $productMaterials[$i]['refRawMaterial'];
                // $data['product'] = $productMaterials[$i]['nameRawMaterial'];

                // // Obtener id producto compuesto
                // $findProduct = $productsDao->findProduct($data, $id_company);
                // if (!$findProduct) {
                //     $i = $i + 2;
                //     $dataImportProductsMaterials = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                //     break;
                // } else {
                //     if ($findProduct['composite'] == 0) {
                //         $i = $i + 2;
                //         $dataImportProductsMaterials = array('error' => true, 'message' => "Producto no esta definido como compuesto<br>Fila: {$i}");
                //         break;
                //     }
                //     $productMaterials[$i]['compositeProduct'] = $findProduct['id_product'];
                // }

                $findProductsMaterials = $generalCompositeProductsDao->findCompositeProduct($productMaterials[$i], $id_company);
            }

            if (!$findProductsMaterials) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportProductsMaterials['insert'] = $insert;
            $dataImportProductsMaterials['update'] = $update;
        }
    } else
        $dataImportProductsMaterials = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProductsMaterials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductsMaterials', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $productsMaterialsDao,
    $convertDataDao,
    $productsDao,
    $materialsDao,
    $magnitudesDao,
    $unitsDao,
    $conversionUnitsDao,
    $costMaterialsDao,
    $priceProductDao,
    $pricesUSDDao,
    $compositeProductsDao,
    $generalCompositeProductsDao,
    $indirectCostDao,
    $costWorkforceDao
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
    $dataProductMaterial = $request->getParsedBody();

    $dataProductMaterials = sizeof($dataProductMaterial);

    if ($dataProductMaterials > 1) {
        $productMaterials = $productsMaterialsDao->findProductMaterial($dataProductMaterial, $id_company);

        if (!$productMaterials) {
            // $dataProductMaterial = $convertDataDao->strReplaceProductsMaterials($dataProductMaterial);
            $productMaterials = $productsMaterialsDao->insertProductsMaterialsByCompany($dataProductMaterial, $id_company);
            //Metodo calcular precio total materias
            if ($productMaterials == null) {
                // Consultar todos los datos del producto
                $products = $productsMaterialsDao->findAllProductsmaterialsByIdProduct($dataProductMaterial['idProduct'], $id_company);

                foreach ($products as $arr) {
                    // Obtener materia prima
                    $material = $materialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                    // Convertir unidades
                    $quantities = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                    // Modificar costo
                    $materialsDao->updateCostProductMaterial($arr, $quantities);
                }
                if ($_SESSION['flag_composite_product'] == '1')
                    $dataMaterial = $costMaterialsDao->calcCostMaterialByCompositeProduct($dataProductMaterial, $id_company);
                else
                    $dataMaterial = $costMaterialsDao->calcCostMaterial($dataProductMaterial, $id_company);

                $productMaterials = $costMaterialsDao->updateCostMaterials($dataMaterial, $id_company);
            }

            $data = [];
            // Calcular Precio del producto
            if ($productMaterials == null)
                $data = $priceProductDao->calcPrice($dataProductMaterial['idProduct']);
            if (isset($data['totalPrice']))
                $productMaterials = $productsDao->updatePrice($dataProductMaterial['idProduct'], $data['totalPrice']);

            if ($productMaterials == null && isset($data['totalPrice']) && $_SESSION['flag_currency_usd'] == '1') {
                // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $dataProductMaterial['idProduct'];

                $productMaterials = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }

            if ($productMaterials == null && $_SESSION['flag_composite_product'] == '1') {
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProductMaterial['idProduct']);

                foreach ($productsCompositer as $j) {
                    if (isset($productMaterials['info'])) break;

                    $data = [];
                    $data['idProduct'] = $j['id_product'];
                    $data['compositeProduct'] = $j['id_child_product'];

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $productMaterials = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($productMaterials['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $productMaterials = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($productMaterials['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $productMaterials = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($productMaterials['info'])) break;

                    if ($_SESSION['flag_currency_usd'] == '1') {
                        // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $j['id_product'];

                        $productMaterials = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }

                    if (isset($productMaterials['info'])) break;

                    $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                    foreach ($productsCompositer2 as $k) {
                        if (isset($productMaterials['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $k['id_child_product'];
                        $data['idProduct'] = $k['id_product'];

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $productMaterials = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($productMaterials['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $productMaterials = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($productMaterials['info'])) break;

                        $data = $priceProductDao->calcPrice($k['id_product']);

                        if (isset($data['totalPrice']))
                            $productMaterials = $productsDao->updatePrice($k['id_product'], $data['totalPrice']);

                        if (isset($productMaterials['info'])) break;

                        if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                            $l = [];
                            $l['price'] = $data['totalPrice'];
                            $l['sale_price'] = $data['sale_price'];
                            $l['id_product'] = $k['id_product'];

                            $resolution = $pricesUSDDao->calcPriceUSDandModify($l, $coverage_usd);
                        }
                    }
                }
            }
            if ($productMaterials == null)
                $resp = array('success' => true, 'message' => 'Materia prima asignada correctamente');
            else if (isset($productMaterials['info']))
                $resp = array('info' => true, 'message' => $productMaterials['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras asignaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'El material ya existe. Ingrese nuevo material');
    } else {
        $productMaterials = $dataProductMaterial['importProductsMaterials'];

        for ($i = 0; $i < sizeof($productMaterials); $i++) {
            // Obtener id producto
            // $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            // $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($productMaterials[$i]);
            $productMaterials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($productMaterials[$i]);
            $productMaterials[$i]['unit'] = $unit['id_unit'];

            if (strtoupper(trim($productMaterials[$i]['type'])) == 'MATERIAL') { // Obtener id materia prima
                // $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
                // $productMaterials[$i]['material'] = $findMaterial['id_material'];

                $findProductsMaterials = $productsMaterialsDao->findProductMaterial($productMaterials[$i], $id_company);

                $productMaterials[$i] = $convertDataDao->strReplaceProductsMaterials($productMaterials[$i]);

                if (!$findProductsMaterials) $resolution = $productsMaterialsDao->insertProductsMaterialsByCompany($productMaterials[$i], $id_company);
                else {
                    $productMaterials[$i]['idProductMaterial'] = $findProductsMaterials['id_product_material'];
                    $resolution = $productsMaterialsDao->updateProductsMaterials($productMaterials[$i]);
                }

                //Metodo calcular precio total materias
                if ($resolution != null) break;

                // Consultar todos los datos del producto
                $products = $productsMaterialsDao->findAllProductsmaterialsByIdProduct($productMaterials[$i]['idProduct'], $id_company);

                // $totalQuantity = 0;

                foreach ($products as $arr) {
                    // Obtener materia prima
                    $material = $materialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                    // Convertir unidades
                    $quantities = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                    // Modificar costo
                    $materialsDao->updateCostProductMaterial($arr, $quantities);
                }

                if ($_SESSION['flag_composite_product'] == '1')
                    $dataMaterial = $costMaterialsDao->calcCostMaterialByCompositeProduct($productMaterials[$i], $id_company);
                else
                    $dataMaterial = $costMaterialsDao->calcCostMaterial($productMaterials[$i], $id_company);

                $resolution = $costMaterialsDao->updateCostMaterials($dataMaterial, $id_company);
            } else {
                // Obtener id producto compuesto
                // $data = [];
                // $data['referenceProduct'] = $productMaterials[$i]['refRawMaterial'];
                // $data['product'] = $productMaterials[$i]['nameRawMaterial'];

                // // Obtener id producto compuesto
                // $findProduct = $productsDao->findProduct($data, $id_company);

                // $productMaterials[$i]['compositeProduct'] = $findProduct['id_product'];

                $composite = $generalCompositeProductsDao->findCompositeProduct($productMaterials[$i]);

                if (!$composite) {
                    $resolution = $compositeProductsDao->insertCompositeProductByCompany($productMaterials[$i], $id_company);
                } else {
                    $productMaterials[$i]['idCompositeProduct'] = $composite['id_composite_product'];
                    $resolution = $compositeProductsDao->updateCompositeProduct($productMaterials[$i]);
                }

                if ($resolution == null) {
                    /* Calcular costo indirecto */
                    // Buscar la maquina asociada al producto
                    $dataProductMachine = $indirectCostDao->findMachineByProduct($productMaterials[$i]['idProduct'], $id_company);
                    // Cambiar a 0
                    $indirectCostDao->updateCostIndirectCostByProduct(0, $productMaterials[$i]['idProduct']);
                    // Calcular costo indirecto
                    $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                    // Actualizar campo
                    $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $productMaterials[$i]['idProduct'], $id_company);
                }


                if ($resolution == null) {
                    // Calcular costo nomina total
                    $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($productMaterials[$i]['idProduct'], $id_company);

                    $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $productMaterials[$i]['idProduct'], $id_company);
                }


                // Calcular costo materia prima compuesta
                if ($resolution == null) {
                    $productMaterials[$i] = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($productMaterials[$i]);
                    $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($productMaterials[$i]);
                }

                // Calcular costo materia prima
                if ($resolution == null) {
                    $productMaterials[$i] = $costMaterialsDao->calcCostMaterialByCompositeProduct($productMaterials[$i]);
                    $resolution = $costMaterialsDao->updateCostMaterials($productMaterials[$i], $id_company);
                }
            }

            // Calcular Precio del producto
            if ($resolution != null) break;
            $data = $priceProductDao->calcPrice($productMaterials[$i]['idProduct']);

            if (isset($data['totalPrice']))
                $resolution = $productsDao->updatePrice($productMaterials[$i]['idProduct'], $data['totalPrice']);

            if (isset($resolution['info'])) break;

            if ($_SESSION['flag_currency_usd'] == '1') {
                // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $productMaterials[$i]['idProduct'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($resolution['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($productMaterials[$i]['idProduct']);

                foreach ($productsCompositer as $j) {
                    if (isset($resolution['info'])) break;

                    $data = [];
                    $data['idProduct'] = $j['id_product'];
                    $data['compositeProduct'] = $j['id_child_product'];

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($resolution['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($resolution['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $resolution = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($resolution['info'])) break;

                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $j['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }

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
                            $resolution = $productsDao->updatePrice($k['id_product'], $data['totalPrice']);

                        if (isset($resolution['info'])) break;

                        if ($_SESSION['flag_currency_usd'] == '1') {
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
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia prima importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importada la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProductsMaterials', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $productsMaterialsDao,
    $convertDataDao,
    $materialsDao,
    $conversionUnitsDao,
    $costMaterialsDao,
    $priceProductDao,
    $pricesUSDDao,
    $productsDao,
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
    $dataProductMaterial = $request->getParsedBody();

    $data = [];

    $productMaterials = $productsMaterialsDao->findProductMaterial($dataProductMaterial, $id_company);

    !is_array($productMaterials) ? $data['id_product_material'] = 0 : $data = $productMaterials;

    if ($data['id_product_material'] == $dataProductMaterial['idProductMaterial'] || $data['id_product_material'] == 0) {
        // $dataProductMaterial = $convertDataDao->strReplaceProductsMaterials($dataProductMaterial);
        $productMaterials = $productsMaterialsDao->updateProductsMaterials($dataProductMaterial);

        //Metodo calcular precio total materias
        if ($productMaterials == null) {
            // Consultar todos los datos del producto
            $products = $productsMaterialsDao->findAllProductsmaterialsByIdProduct($dataProductMaterial['idProduct'], $id_company);

            // $totalQuantity = 0;

            foreach ($products as $arr) {
                // Obtener materia prima
                $material = $materialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                // Convertir unidades
                $quantities = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                // Modificar costo
                $materialsDao->updateCostProductMaterial($arr, $quantities);
            }
            $status = false;

            if ($_SESSION['flag_composite_product'] == '1') {
                $composite = $generalCompositeProductsDao->findCompositeProductCost($dataProductMaterial['idProduct']);

                !$composite ? $status = false : $status = true;

                if ($status == true)
                    $dataMaterial = $costMaterialsDao->calcCostMaterialByCompositeProduct($dataProductMaterial, $id_company);
            }

            if ($_SESSION['flag_composite_product'] == '0' || $status == false)
                $dataMaterial = $costMaterialsDao->calcCostMaterial($dataProductMaterial, $id_company);

            $productMaterials = $costMaterialsDao->updateCostMaterials($dataMaterial, $id_company);
        }

        // Calcular Precio del producto
        if ($productMaterials == null)
            $data = $priceProductDao->calcPrice($dataProductMaterial['idProduct']);
        if (isset($data['totalPrice']))
            $productMaterials = $productsDao->updatePrice($dataProductMaterial['idProduct'], $data['totalPrice']);

        // Convertir a Dolares 
        if ($productMaterials == null && isset($data['totalPrice']) && $_SESSION['flag_currency_usd'] == '1') {
            $k = [];
            $k['price'] = $data['totalPrice'];
            $k['sale_price'] = $data['sale_price'];
            $k['id_product'] = $dataProductMaterial['idProduct'];

            $productMaterials = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
        }

        if ($productMaterials == null && $_SESSION['flag_composite_product'] == '1') {
            // Calcular costo material porq
            $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProductMaterial['idProduct']);

            foreach ($productsCompositer as $j) {
                if (isset($productMaterials['info'])) break;

                $data = [];
                $data['idProduct'] = $j['id_product'];
                $data['compositeProduct'] = $j['id_child_product'];

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $productMaterials = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($productMaterials['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $productMaterials = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($productMaterials['info'])) break;

                $data = $priceProductDao->calcPrice($j['id_product']);

                if (isset($data['totalPrice']))
                    $productMaterials = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                if (isset($productMaterials['info'])) break;

                if ($_SESSION['flag_currency_usd'] == '1') {
                    // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $data['totalPrice'];
                    $k['sale_price'] = $data['sale_price'];
                    $k['id_product'] = $j['id_product'];

                    $productMaterials = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                }

                if (isset($productMaterials['info'])) break;

                $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                foreach ($productsCompositer2 as $k) {
                    if (isset($productMaterials['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $k['id_child_product'];
                    $data['idProduct'] = $k['id_product'];

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $productMaterials = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($productMaterials['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $productMaterials = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($productMaterials['info'])) break;

                    $data = $priceProductDao->calcPrice($k['id_product']);

                    if (isset($data['totalPrice']))
                        $productMaterials = $productsDao->updatePrice($k['id_product'], $data['totalPrice']);

                    if (isset($productMaterials['info'])) break;

                    if ($_SESSION['flag_currency_usd'] == '1') {
                        // Convertir a Dolares
                        $l = [];
                        $l['price'] = $data['totalPrice'];
                        $l['sale_price'] = $data['sale_price'];
                        $l['id_product'] = $k['id_product'];

                        $productMaterials = $pricesUSDDao->calcPriceUSDandModify($l, $coverage_usd);
                    }
                }
            }
        }
        if ($productMaterials == null)
            $resp = array('success' => true, 'message' => 'Materia prima actualizada correctamente');
        else if (isset($productMaterials['info']))
            $resp = array('info' => true, 'message' => $productMaterials['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El material ya existe. Ingrese nuevo material');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProductMaterial', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $productsMaterialsDao,
    $costMaterialsDao,
    $materialsDao,
    $conversionUnitsDao,
    $priceProductDao,
    $pricesUSDDao,
    $productsDao,
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
    $dataProductMaterial = $request->getParsedBody();

    $product = $productsMaterialsDao->deleteProductMaterial($dataProductMaterial);

    //Metodo calcular precio total materias
    if ($product == null) {

        // Consultar todos los datos del producto
        $products = $productsMaterialsDao->findAllProductsmaterialsByIdProduct($dataProductMaterial['idProduct'], $id_company);

        // $totalQuantity = 0;

        foreach ($products as $arr) {
            // Obtener materia prima
            $material = $materialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

            // Convertir unidades
            $quantities = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

            // Modificar costo
            $materialsDao->updateCostProductMaterial($arr, $quantities);
        }
        if ($_SESSION['flag_composite_product'] == '1')
            $dataMaterial = $costMaterialsDao->calcCostMaterialByCompositeProduct($dataProductMaterial, $id_company);
        else
            $dataMaterial = $costMaterialsDao->calcCostMaterial($dataProductMaterial, $id_company);

        $product = $costMaterialsDao->updateCostMaterials($dataMaterial, $id_company);
    }

    $data = [];
    // Calcular Precio del producto
    if ($product == null)
        $data = $priceProductDao->calcPrice($dataProductMaterial['idProduct']);
    if (isset($data['totalPrice']))
        $product = $productsDao->updatePrice($dataProductMaterial['idProduct'], $data['totalPrice']);

    if ($product == null && isset($data['totalPrice']) && $_SESSION['flag_currency_usd'] == '1') {
        // Convertir a Dolares 
        $k = [];
        $k['price'] = $data['totalPrice'];
        $k['sale_price'] = $data['sale_price'];
        $k['id_product'] = $dataProductMaterial['idProduct'];

        $product = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
    }

    if ($product == null && $_SESSION['flag_composite_product'] == '1') {
        // Calcular costo material porq
        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProductMaterial['idProduct']);

        foreach ($productsCompositer as $j) {
            if (isset($product['info'])) break;

            $data = [];
            $data['idProduct'] = $j['id_product'];
            $data['compositeProduct'] = $j['id_child_product'];

            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
            $product = $generalCompositeProductsDao->updateCostCompositeProduct($data);

            if (isset($product['info'])) break;
            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
            $product = $costMaterialsDao->updateCostMaterials($data, $id_company);

            if (isset($product['info'])) break;

            $data = $priceProductDao->calcPrice($j['id_product']);

            if (isset($data['totalPrice']))
                $product = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

            if (isset($product['info'])) break;

            if ($_SESSION['flag_currency_usd'] == '1') {
                // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $j['id_product'];

                $product = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }

            if (isset($product['info'])) break;

            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

            foreach ($productsCompositer2 as $k) {
                if (isset($product['info'])) break;

                $data = [];
                $data['compositeProduct'] = $k['id_child_product'];
                $data['idProduct'] = $k['id_product'];

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $product = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($product['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $product = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($product['info'])) break;

                $data = $priceProductDao->calcPrice($k['id_product']);

                if (isset($data['totalPrice']))
                    $product = $productsDao->updatePrice($k['id_product'], $data['totalPrice']);

                if (isset($product['info'])) break;

                if ($_SESSION['flag_currency_usd'] == '1') {
                    // Convertir a Dolares 
                    $l = [];
                    $l['price'] = $data['totalPrice'];
                    $l['sale_price'] = $data['sale_price'];
                    $l['id_product'] = $k['id_product'];

                    $product = $pricesUSDDao->calcPriceUSDandModify($l, $coverage_usd);
                }
            }
        }
    }

    if ($product == null)
        $resp = array('success' => true, 'message' => 'Materia prima eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la materia prima asignada, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
