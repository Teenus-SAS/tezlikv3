<?php

use tezlikv3\dao\InventoryDao;
use tezlikv3\dao\InvMoldsDao;
use tezlikv3\dao\PlanMaterialsDao;
use tezlikv3\dao\PlanProductsDao;
use tezlikv3\dao\ClassificationDao;
use tezlikv3\dao\invCategoriesDao;
use tezlikv3\dao\UnitSalesDao;

$inventoryDao = new InventoryDao();
$categoriesDao = new invCategoriesDao();
$moldsDao = new InvMoldsDao();
$productsDao = new PlanProductsDao();
$unitSalesDao = new UnitSalesDao();
$materialsDao = new PlanMaterialsDao();
$classificationDao = new ClassificationDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/inventory', function (Request $request, Response $response, $args) use ($inventoryDao, $productsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // Insumos
    $supplies = $inventoryDao->findAllInventoryMaterialsAndSupplies($id_company, 1);
    // Materias Prima
    $rawMaterials = $inventoryDao->findAllInventoryMaterialsAndSupplies($id_company, 2);
    // Productos
    $products = $productsDao->findAllProductsByCompany($id_company);

    $inventory['supplies'] = $supplies;
    $inventory['rawMaterials'] = $rawMaterials;
    $inventory['products'] = $products;

    $response->getBody()->write(json_encode($inventory, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/inventoryDataValidation', function (Request $request, Response $response, $args) use ($categoriesDao, $productsDao, $materialsDao, $moldsDao, $unitSalesDao) {
    $dataInventory = $request->getParsedBody();

    if (isset($dataInventory)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $update = 0;

        $inventory = $dataInventory['importInventory'];

        for ($i = 0; $i < sizeof($inventory); $i++) {

            // Obtener id categoria
            $findCategory = $categoriesDao->findCategory($inventory[$i]);

            if (!$findCategory) {
                $i = $i + 1;
                $dataImportinventory = array('error' => true, 'message' => "Categoria no existe en la base de datos. Fila: {$i}");
                break;
            }

            if (
                empty($inventory[$i]['reference']) || empty($inventory[$i]['nameInventory']) ||
                empty($inventory[$i]['quantity']) || empty($inventory[$i]['category'])
            ) {
                $i = $i + 1;
                $dataImportinventory = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }
            $category = $inventory[$i]['category'];

            if ($category == 'Productos') {
                if (empty($inventory[$i]['referenceMold']) || empty($inventory[$i]['mold'])) {
                    $i = $i + 1;
                    $dataImportinventory = array('error' => true, 'message' => "Moldes vacios en la fila: {$i}");
                    break;
                }

                // Obtener id Molde
                $findMold = $moldsDao->findInvMold($inventory[$i], $id_company);
                if (!$findMold) {
                    $i = $i + 1;
                    $dataImportinventory = array('error' => true, 'message' => "Molde no existe en la base de datos.<br>Fila: {$i}");
                    break;
                }
            }

            if ($category == 'Materiales' || $category == 'Insumos') {
                if (empty($inventory[$i]['unityRawMaterial'])) {
                    $i = $i + 1;
                    $dataImportinventory = array('error' => true, 'message' => "Unidad vacia en la fila: {$i}");
                    break;
                }
            }

            if ($category == 'Productos') {
                $inventory[$i]['referenceProduct'] = $inventory[$i]['reference'];
                $inventory[$i]['product'] = $inventory[$i]['nameInventory'];

                // Consultar si existe producto
                $findProduct = $productsDao->findProduct($inventory[$i], $id_company);
                // Consultar si existe en tabla unit_sales
                $inventory[$i]['idProduct'] = $findProduct['id_product'];
                $unitSales = $unitSalesDao->findSales($inventory[$i], $id_company);
                if (!$findProduct || $unitSales) {
                    // Almacenar inventarios no existentes
                    $dataImportinventory['reference'][$i] = $inventory[$i]['reference'];
                    $dataImportinventory['nameInventory'][$i] = $inventory[$i]['nameInventory'];
                    unset($inventory[$i]);
                } else {
                    $update = $update + 1;
                }
            }
            if ($category == 'Materiales' || $category == 'Insumos') {
                $inventory[$i]['refRawMaterial'] = $inventory[$i]['reference'];
                $inventory[$i]['nameRawMaterial'] = $inventory[$i]['nameInventory'];

                $findMaterial = $materialsDao->findMaterial($inventory[$i], $id_company);
                if (!$findMaterial) {
                    // Almacenar inventarios no existentes
                    $dataImportinventory['reference'][$i] = $inventory[$i]['reference'];
                    $dataImportinventory['nameInventory'][$i] = $inventory[$i]['nameInventory'];
                    unset($inventory[$i]);
                } else $update = $update + 1;
            }
            // Resetear llaves
            $inventory = array_values($inventory);
            if (isset($dataImportinventory)) {
                $dataImportinventory['reference'] = array_values($dataImportinventory['reference']);
                $dataImportinventory['nameInventory'] = array_values($dataImportinventory['nameInventory']);
            }
        }
        $dataImportinventory['update'] = $update;
        // Almacenar inventarios existentes
        $_SESSION['dataImportInventory'] = $inventory;
    } else
        $dataImportinventory = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportinventory, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addInventory', function (Request $request, Response $response, $args) use ($productsDao, $materialsDao, $moldsDao, $classificationDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $inventory = $_SESSION['dataImportInventory'];

    for ($i = 0; $i < sizeof($inventory); $i++) {
        $category = $inventory[$i]['category'];
        // Producto
        if ($category == 'Productos') {
            // Obtener id molde
            $findMold = $moldsDao->findInvMold($inventory[$i], $id_company);
            $inventory[$i]['idMold'] = $findMold['id_mold'];

            //$inventory[$i]['referenceProduct'] = $inventory[$i]['reference'];
            //$inventory[$i]['product'] = $inventory[$i]['nameInventory'];

            $findProduct = $productsDao->findProduct($inventory[$i], $id_company);
            /*if (!$findProduct)
                $resolution = $productsDao->insertProductByCompany($inventory[$i], $id_company);
            else {}*/
            // $inventory[$i]['idProduct'] = $findProduct['id_product'];
            $resolution = $productsDao->updateProductByCompany($inventory[$i], $id_company);

            // Calcular clasificación
            $inventory[$i]['cantMonths'] = 3;
            $classification = $classificationDao->calcClassificationByProduct($inventory[$i], $id_company);
        }

        // Materia prima y Insumos
        if ($category == 'Materiales' || $category == 'Insumos') {
            //$inventory[$i]['refRawMaterial'] = $inventory[$i]['reference'];
            //$inventory[$i]['nameRawMaterial'] = $inventory[$i]['nameInventory'];

            $findMaterial = $materialsDao->findMaterial($inventory[$i], $id_company);
            // if (!$findMaterial) $resolution = $materialsDao->insertMaterialsByCompany($inventory[$i], $id_company);
            // else {}
            $inventory[$i]['idMaterial'] = $findMaterial['id_material'];
            $resolution = $materialsDao->updateMaterialsByCompany($inventory[$i]);
        }
    }
    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Inventario importado correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteInventorySession', function (Request $request, Response $response, $args) {
    //Eliminar variable session
    session_start();
    unset($_SESSION['dataImportInventory']);
    $response->getBody()->write(json_encode(JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
