<?php

use tezlikv3\dao\InventoryDao;
use tezlikv3\dao\PlanMaterialsDao;
use tezlikv3\dao\PlanProductsDao;

$inventoryDao = new InventoryDao();
$productsDao = new PlanProductsDao();
$materialsDao = new PlanMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Materias prima y Insumos */

$app->get('/inventory/{category}', function (Request $request, Response $response, $args) use ($inventoryDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $supplies = $inventoryDao->findAllInventoryMaterialsAndSupplies($id_company, $args['category']);
    $response->getBody()->write(json_encode($supplies, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/inventoryDataValidation', function (Request $request, Response $response, $args) use ($productsDao, $materialsDao) {
    $dataInventory = $request->getParsedBody();

    if (isset($dataInventory)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $inventory = $dataInventory['importInventory'];

        for ($i = 0; $i < sizeof($inventory); $i++) {
            $reference = $inventory[$i]['reference'];
            $name = $inventory[$i]['nameInventory'];
            $quantity = $inventory[$i]['quantity'];
            $category = $inventory[$i]['category'];
            if (empty($reference) || empty($name) || empty($quantity) || empty($category)) {
                $i = $i + 1;
                $dataImportinventory = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }
            if ($category == 'Material' || $category == 'Insumo') {
                $unityRawMaterial = $inventory[$i]['unityRawMaterial'];
                if (empty($unityRawMaterial)) {
                    $i = $i + 1;
                    $dataImportinventory = array('error' => true, 'message' => "Unidad vacia en la fila: {$i}");
                    break;
                }
            }

            if ($category == 'Productos') {
                $inventory[$i]['referenceProduct'] = $inventory[$i]['reference'];
                $inventory[$i]['product'] = $inventory[$i]['nameInventory'];

                $findProduct = $productsDao->findProduct($inventory[$i], $id_company);
                !$findProduct ? $insert = $insert + 1 : $update = $update + 1;
            }
            if ($category == 'Materiales' || $category == 'Insumos') {
                $inventory[$i]['refRawMaterial'] = $inventory[$i]['reference'];
                $inventory[$i]['nameRawMaterial'] = $inventory[$i]['nameInventory'];

                $findMaterial = $materialsDao->findMaterial($inventory[$i], $id_company);
                !$findMaterial ? $insert = $insert + 1 : $update = $update + 1;
            }

            $dataImportinventory['insert'] = $insert;
            $dataImportinventory['update'] = $update;
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
        $category = $inventory[$i]['category'];
        // Producto
        if ($category == 'Productos') {
            $inventory[$i]['referenceProduct'] = $inventory[$i]['reference'];
            $inventory[$i]['product'] = $inventory[$i]['nameInventory'];
            $inventory[$i]['product'] = $inventory[$i]['nameInventory'];

            $findProduct = $productsDao->findProduct($inventory[$i], $id_company);
            if (!$findProduct)
                $product = $productsDao->insertProductByCompany($inventory[$i], $id_company);
            else {
                $inventory[$i]['idProduct'] = $findProduct['id_product'];
                $product = $productsDao->updateProductByCompany($inventory[$i], $id_company);
            }
        }

        // Materia prima y Insumos
        if ($category == 'Materiales' || $category == 'Insumos') {
            $inventory[$i]['refRawMaterial'] = $inventory[$i]['reference'];
            $inventory[$i]['nameRawMaterial'] = $inventory[$i]['nameInventory'];

            $findMaterial = $materialsDao->findMaterial($inventory[$i], $id_company);
            if (!$findMaterial) $material = $materialsDao->insertMaterialsByCompany($inventory[$i], $id_company);
            else {
                $inventory[$i]['idMaterial'] = $findMaterial['id_material'];
                $material = $materialsDao->updateMaterialsByCompany($inventory[$i]);
            }
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
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
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
