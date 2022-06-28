<?php

use tezlikv3\dao\InventoryDao;
use tezlikv3\dao\PlanMaterialsDao;
use tezlikv3\dao\PlanProductsDao;

$inventoryDao = new InventoryDao();
$productsDao = new PlanProductsDao();
$materialsDao = new PlanMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/*$app->get('/inventory', function (Request $request, Response $response, $args) use ($inventoryDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $inventory = $inventoryDao->findAllInventory($id_company);
    $response->getBody()->write(json_encode($inventory, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});*/

$app->post('/inventoryDataValidation', function (Request $request, Response $response, $args) use ($productsDao, $materialsDao) {
    $dataInventory = $request->getParsedBody();

    if (isset($dataInventory)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $inventory = $dataInventory['importInventory'];

        for ($i = 0; $i < sizeof($inventory); $i++) {
            $referenceProduct = $inventory[$i]['referenceProduct'];
            $product = $inventory[$i]['product'];
            $quantityProduct = $inventory[$i]['quantityProduct'];
            $refRawMaterial = $inventory[$i]['refRawMaterial'];
            $nameRawMaterial = $inventory[$i]['nameRawMaterial'];
            $unityRawMaterial = $inventory[$i]['unityRawMaterial'];
            $quantityRawMaterial = $inventory[$i]['quantityRawMaterial'];
            if (
                empty($referenceProduct) || empty($product) || empty($quantityProduct) || empty($refRawMaterial) ||
                empty($nameRawMaterial) || empty($unityRawMaterial) || empty($quantityRawMaterial)
            ) {
                $i = $i + 1;
                $dataImportinventory = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            } else {
                $findProduct = $productsDao->findProduct($inventory[$i], $id_company);
                $findMaterial = $materialsDao->findMaterial($inventory[$i], $id_company);
                !$findProduct ? $insert = $insert + 1 : $update = $update + 1;
                !$findMaterial ? $insert = $insert + 1 : $update = $update + 1;
                $dataImportinventory['insert'] = $insert;
                $dataImportinventory['update'] = $update;
            }
        }
    } else
        $dataImportinventory = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportinventory, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addInventory', function (Request $request, Response $response, $args) use ($productsDao, $materialsDao) {
    session_start();
    $dataInventory = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $inventory = $dataInventory['importInventory'];

    for ($i = 0; $i < sizeof($inventory); $i++) {
        // Producto
        $findProduct = $productsDao->findProduct($inventory[$i], $id_company);
        if (!$findProduct)
            $product = $productsDao->insertProductByCompany($inventory[$i], $id_company);
        else {
            $inventory[$i]['idProduct'] = $findProduct['id_product'];
            $product = $productsDao->updateProductByCompany($inventory[$i], $id_company);
        }

        // Materia prima
        $findMaterial = $materialsDao->findMaterial($inventory[$i], $id_company);
        if (!$findMaterial) $material = $materialsDao->insertMaterialsByCompany($inventory[$i], $id_company);
        else {
            $inventory[$i]['idMaterial'] = $findMaterial['id_material'];
            $material = $materialsDao->updateMaterialsByCompany($inventory[$i]);
        }
    }
    if ($product == null && $material == null)
        $resp = array('success' => true, 'message' => 'Inventario importado correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

/*$app->post('/updateinventory', function (Request $request, Response $response, $args) use ($inventoryDao) {
    $dataInventory = $request->getParsedBody();

    if (empty($dataInventory['inventory']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $inventory = $inventoryDao->updateinventory($dataInventory);

        if ($inventory == null)
            $resp = array('success' => true, 'message' => 'Proceso actualizado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteinventory/{id_inventory}', function (Request $request, Response $response, $args) use ($inventoryDao) {
    $inventory = $inventoryDao->deleteinventory($args['id_inventory']);

    if ($inventory == null)
        $resp = array('success' => true, 'message' => 'Proceso eliminado correctamente');

    if ($inventory != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar el proceso, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});*/
