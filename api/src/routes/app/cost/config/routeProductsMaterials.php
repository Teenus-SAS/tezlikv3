<?php

use tezlikv3\dao\CompositeProductsDao;
use tezlikv3\Dao\ConversionUnitsDao;
use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\GeneralProductMaterialsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\ProductsMaterialsDao;
use tezlikv3\Dao\MagnitudesDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\dao\UnitsDao;

$productsMaterialsDao = new ProductsMaterialsDao();
$generalProductMaterialsDao = new GeneralProductMaterialsDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new GeneralProductsDao();
$materialsDao = new GeneralMaterialsDao();
$costMaterialsDao = new CostMaterialsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$priceProductDao = new PriceProductDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productsMaterials/{idProduct}', function (Request $request, Response $response, $args) use ($productsMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productMaterials = $productsMaterialsDao->findAllProductsmaterialsByIdProduct($args['idProduct'], $id_company);

    $response->getBody()->write(json_encode($productMaterials));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/allProductsMaterials', function (Request $request, Response $response, $args) use ($generalProductMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productMaterials = $generalProductMaterialsDao->findAllProductsmaterials($id_company);

    $response->getBody()->write(json_encode($productMaterials));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/productsMaterialsDataValidation', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao,
    $magnitudesDao,
    $unitsDao,
    $productsDao,
    $materialsDao
) {
    $dataProductMaterial = $request->getParsedBody();

    if (isset($dataProductMaterial)) {
        session_start();
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


            if (
                empty($productMaterials[$i]['referenceProduct']) || empty($productMaterials[$i]['product']) || empty($productMaterials[$i]['refRawMaterial']) ||
                empty($productMaterials[$i]['nameRawMaterial']) || $productMaterials[$i]['quantity'] == ''
            ) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }
            if (
                empty(trim($productMaterials[$i]['referenceProduct'])) || empty(trim($productMaterials[$i]['product'])) || empty(trim($productMaterials[$i]['refRawMaterial'])) ||
                empty(trim($productMaterials[$i]['nameRawMaterial'])) || trim($productMaterials[$i]['quantity']) == ''
            ) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            $quantity = str_replace(',', '.', $productMaterials[$i]['quantity']);

            $quantity = 1 * $quantity;

            if ($quantity <= 0 || is_nan($quantity)) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "La cantidad debe ser mayor a cero (0)<br>Fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id materia prima
            $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
            if (!$findMaterial) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Materia prima no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $productMaterials[$i]['material'] = $findMaterial['id_material'];


            $findProductsMaterials = $productsMaterialsDao->findProductMaterial($productMaterials[$i], $id_company);
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
    $productsMaterialsDao,
    $convertDataDao,
    $productsDao,
    $materialsDao,
    $magnitudesDao,
    $unitsDao,
    $conversionUnitsDao,
    $costMaterialsDao,
    $priceProductDao,
    $generalCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();

    $dataProductMaterials = sizeof($dataProductMaterial);

    if ($dataProductMaterials > 1) {

        $productMaterials = $productsMaterialsDao->findProductMaterial($dataProductMaterial, $id_company);

        if (!$productMaterials) {
            $dataProductMaterial = $convertDataDao->strReplaceProductsMaterials($dataProductMaterial);

            $productMaterials = $productsMaterialsDao->insertProductsMaterialsByCompany($dataProductMaterial, $id_company);
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
                if ($_SESSION['flag_composite_product'] == '1')
                    $dataMaterial = $costMaterialsDao->calcCostMaterialByCompositeProduct($dataProductMaterial, $id_company);
                else
                    $dataMaterial = $costMaterialsDao->calcCostMaterial($dataProductMaterial, $id_company);

                $productMaterials = $costMaterialsDao->updateCostMaterials($dataMaterial, $id_company);
            }

            // Calcular Precio del producto
            if ($productMaterials == null)
                $productMaterials = $priceProductDao->calcPrice($dataProductMaterial['idProduct']);
            if (isset($productMaterials['totalPrice']))
                $productMaterials = $productsDao->updatePrice($dataProductMaterial['idProduct'], $productMaterials['totalPrice']);

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
                    $productMaterials = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

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
                        $productMaterials = $productsDao->updatePrice($k['id_product'], $data['totalPrice']);
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
            $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id materia prima
            $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
            $productMaterials[$i]['material'] = $findMaterial['id_material'];

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($productMaterials[$i]);
            $productMaterials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($productMaterials[$i]);
            $productMaterials[$i]['unit'] = $unit['id_unit'];

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

            // Calcular Precio del producto
            if ($resolution != null) break;
            $resolution = $priceProductDao->calcPrice($productMaterials[$i]['idProduct']);

            if (isset($resolution['info']))
                break;

            $resolution = $productsDao->updatePrice($productMaterials[$i]['idProduct'], $resolution['totalPrice']);

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
                    $resolution = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

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
                        $resolution = $productsDao->updatePrice($k['id_product'], $data['totalPrice']);
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
    $productsMaterialsDao,
    $convertDataDao,
    $materialsDao,
    $conversionUnitsDao,
    $costMaterialsDao,
    $priceProductDao,
    $productsDao,
    $generalCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();

    $data = [];

    $productMaterials = $productsMaterialsDao->findProductMaterial($dataProductMaterial, $id_company);

    !is_array($productMaterials) ? $data['id_productMaterial'] = 0 : $data = $productMaterials;

    if ($data['id_product_material'] == $dataProductMaterial['idProductMaterial'] || $data['id_productMaterial'] == 0) {
        $dataProductMaterial = $convertDataDao->strReplaceProductsMaterials($dataProductMaterial);
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
            $productMaterials = $priceProductDao->calcPrice($dataProductMaterial['idProduct']);
        if (isset($productMaterials['totalPrice']))
            $productMaterials = $productsDao->updatePrice($dataProductMaterial['idProduct'], $productMaterials['totalPrice']);

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
                $productMaterials = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

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
                    $productMaterials = $productsDao->updatePrice($k['id_product'], $data['totalPrice']);
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
    $productsMaterialsDao,
    $costMaterialsDao,
    $materialsDao,
    $conversionUnitsDao,
    $priceProductDao,
    $productsDao,
    $generalCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
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

    // Calcular Precio del producto
    if ($product == null)
        $product = $priceProductDao->calcPrice($dataProductMaterial['idProduct']);
    if (isset($product['totalPrice']))
        $product = $productsDao->updatePrice($dataProductMaterial['idProduct'], $product['totalPrice']);

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
            $product = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

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
                $product = $productsDao->updatePrice($k['id_product'], $data['totalPrice']);
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
