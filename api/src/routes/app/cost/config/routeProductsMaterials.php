<?php

use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralProductsMaterialsDao;
use tezlikv3\dao\PriceProductDao;

$productsMaterialsDao = new GeneralProductsMaterialsDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new GeneralProductsDao();
$materialsDao = new GeneralMaterialsDao();
$costMaterialsDao = new CostMaterialsDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productsMaterials/{idProduct}', function (Request $request, Response $response, $args) use ($productsMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productMaterials = $productsMaterialsDao->findAllProductsmaterials($args['idProduct'], $id_company);

    $response->getBody()->write(json_encode($productMaterials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/productsMaterialsDataValidation', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao,
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
            if (
                empty($productMaterials[$i]['referenceProduct']) || empty($productMaterials[$i]['product']) || empty($productMaterials[$i]['refRawMaterial']) ||
                empty($productMaterials[$i]['nameRawMaterial']) || $productMaterials[$i]['quantity'] == ''
            ) {
                $i = $i + 1;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            $quantity = str_replace(',', '.', $productMaterials[$i]['quantity']);

            $quantity = 1 * $quantity;

            if ($quantity <= 0 || is_nan($quantity)) {
                $i = $i + 1;
                $dataImportProductsMaterials = array('error' => true, 'message' => "La cantidad debe ser mayor a cero (0)<br>Fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 1;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id materia prima
            $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
            if (!$findMaterial) {
                $i = $i + 1;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Materia prima no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $productMaterials[$i]['material'] = $findMaterial['id_material'];


            $findProductsMaterials = $productsMaterialsDao->findProductMaterial($productMaterials[$i]);
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
    $costMaterialsDao,
    $priceProductDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();

    $dataProductMaterials = sizeof($dataProductMaterial);

    if ($dataProductMaterials > 1) {
        $dataProductMaterial = $convertDataDao->strReplaceProductsMaterials($dataProductMaterial);

        $productMaterials = $productsMaterialsDao->insertProductsMaterialsByCompany($dataProductMaterial, $id_company);
        //Metodo calcular precio total materias
        $costMaterials = $costMaterialsDao->calcCostMaterial($dataProductMaterial['idProduct'], $id_company);

        // Calcular Precio del producto
        $priceProduct = $priceProductDao->calcPrice($dataProductMaterial['idProduct']);

        if ($productMaterials == null && $costMaterials == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Materia prima asignada correctamente');
        else if (isset($productMaterials['info']))
            $resp = array('info' => true, 'message' => $productMaterials['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras asignaba la información. Intente nuevamente');
    } else {
        $productMaterials = $dataProductMaterial['importProductsMaterials'];

        for ($i = 0; $i < sizeof($productMaterials); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id materia prima
            $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
            $productMaterials[$i]['material'] = $findMaterial['id_material'];

            $findProductsMaterials = $productsMaterialsDao->findProductMaterial($productMaterials[$i]);

            $productMaterials[$i] = $convertDataDao->strReplaceProductsMaterials($productMaterials[$i]);

            if (!$findProductsMaterials) $resolution = $productsMaterialsDao->insertProductsMaterialsByCompany($productMaterials[$i], $id_company);
            else {
                $productMaterials[$i]['idProductMaterial'] = $findProductsMaterials['id_product_material'];
                $resolution = $productsMaterialsDao->updateProductsMaterials($productMaterials[$i]);
            }

            //Metodo calcular precio total materias
            $costMaterials = $costMaterialsDao->calcCostMaterial($productMaterials[$i]['idProduct'], $id_company);

            // Calcular Precio del producto
            $priceProduct = $priceProductDao->calcPrice($productMaterials[$i]['idProduct']);
        }
        if ($resolution == null && $costMaterials == null && $priceProduct == null)
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
    $costMaterialsDao,
    $priceProductDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();

    $dataProductMaterial = $convertDataDao->strReplaceProductsMaterials($dataProductMaterial);
    $productMaterials = $productsMaterialsDao->updateProductsMaterials($dataProductMaterial);

    //Metodo calcular precio total materias
    $costMaterials = $costMaterialsDao->calcCostMaterial($dataProductMaterial['idProduct'], $id_company);

    // Calcular Precio del producto
    $priceProduct = $priceProductDao->calcPrice($dataProductMaterial['idProduct']);

    if ($productMaterials == null && $costMaterials == null && $priceProduct == null)
        $resp = array('success' => true, 'message' => 'Materia prima actualizada correctamente');
    else if (isset($productMaterials['info']))
        $resp = array('info' => true, 'message' => $productMaterials['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProductMaterial', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao,
    $costMaterialsDao,
    $priceProductDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();

    $product = $productsMaterialsDao->deleteProductMaterial($dataProductMaterial);

    //Metodo calcular precio total materias
    $costMaterials = $costMaterialsDao->calcCostMaterial($dataProductMaterial['idProduct'], $id_company);

    // Calcular Precio del producto
    $priceProduct = $priceProductDao->calcPrice($dataProductMaterial['idProduct']);

    if ($product == null && $costMaterials == null && $priceProduct == null)
        $resp = array('success' => true, 'message' => 'Materia prima eliminada correctamente');

    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la materia prima asignada, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
