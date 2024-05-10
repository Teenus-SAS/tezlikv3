<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\CustomPricesDao;
use tezlikv3\dao\GeneralCustomPricesDao;
use tezlikv3\dao\GeneralPricesListDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\Dao\PriceCustomDao;

$customPricesDao = new CustomPricesDao();
$generalCustomPricesDao = new GeneralCustomPricesDao();
$autenticationDao = new AutenticationUserDao();
$priceCustomDao = new PriceCustomDao();
$lastDataDao = new LastDataDao();
$generalProductsDao = new GeneralProductsDao();
$generalPricesListDao = new GeneralPricesListDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/customPrices', function (Request $request, Response $response, $args) use (
    $customPricesDao,
    $autenticationDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    session_start();
    $id_company = $_SESSION['id_company'];

    $customPrices = $customPricesDao->findAllCustomPricesByCompany($id_company);
    $response->getBody()->write(json_encode($customPrices, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/customDataValidation', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $autenticationDao,
    $generalPricesListDao,
    $generalCustomPricesDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $dataCustom = $request->getParsedBody();

    if (isset($dataCustom)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $custom = $dataCustom['importCustom'];

        for ($i = 0; $i < sizeof($custom); $i++) {
            $typeCustom = $dataCustom['type'];

            if ($typeCustom == 1) {
                if (
                    empty($custom[$i]['referenceProduct']) || empty($custom[$i]['product']) ||
                    empty($custom[$i]['priceName']) || empty($custom[$i]['customPricesValue'])
                ) {
                    $i = $i + 2;
                    $dataImportCustom = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                    break;
                }
                if (
                    empty(trim($custom[$i]['referenceProduct'])) || empty(trim($custom[$i]['product'])) ||
                    empty(trim($custom[$i]['priceName'])) || empty(trim($custom[$i]['customPricesValue']))
                ) {
                    $i = $i + 2;
                    $dataImportCustom = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                    break;
                } else {
                    $findProduct = $generalProductsDao->findProduct($custom[$i], $id_company);

                    if (!$findProduct) {
                        $i = $i + 2;
                        $dataImportCustom = array('error' => true, 'message' => "Producto no existe en la base de datos fila: {$i}");
                        break;
                    } else $custom[$i]['idProduct'] = $findProduct['id_product'];

                    $findPrice = $generalPricesListDao->findPricesList($custom[$i], $id_company);

                    if (!$findPrice) {
                        $i = $i + 2;
                        $dataImportCustom = array('error' => true, 'message' => "Precio de lista no existe en la base de datos fila: {$i}");
                        break;
                    } else $custom[$i]['idPriceList'] = $findPrice['id_price_list'];


                    $findCustomPrice = $generalCustomPricesDao->findCustomPrice($custom[$i], $id_company);
                    if (!$findCustomPrice) $insert = $insert + 1;
                    else $update = $update + 1;
                    $dataImportCustom['insert'] = $insert;
                    $dataImportCustom['update'] = $update;
                }
            } else {
                if (
                    empty($custom[$i]['referenceProduct']) || empty($custom[$i]['product']) ||
                    empty($custom[$i]['priceName']) || empty($custom[$i]['percentage'])
                ) {
                    $i = $i + 2;
                    $dataImportCustom = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                    break;
                }
                if (
                    empty(trim($custom[$i]['referenceProduct'])) || empty(trim($custom[$i]['product'])) ||
                    empty(trim($custom[$i]['priceName'])) || empty(trim($custom[$i]['percentage']))
                ) {
                    $i = $i + 2;
                    $dataImportCustom = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                    break;
                }
            }
        }
    } else
        $dataImportCustom = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportCustom, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addCustomPrice', function (Request $request, Response $response, $args) use (
    $customPricesDao,
    $autenticationDao,
    $generalCustomPricesDao,
    $generalPricesListDao,
    $generalProductsDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    session_start();
    $id_company = $_SESSION['id_company'];

    $dataCustomPrice = $request->getParsedBody();

    $customPrices = $dataCustomPrice['importCustom'];
    $resolution = null;

    for ($i = 0; $i < sizeof($customPrices); $i++) {
        if (isset($resolution['info'])) break;

        // Obtener id producto
        $findProduct = $generalProductsDao->findProduct($customPrices[$i], $id_company);
        $customPrices[$i]['idProduct'] = $findProduct['id_product'];

        // Obtener id precio lista
        $findPrice = $generalPricesListDao->findPricesList($customPrices[$i], $id_company);
        $customPrices[$i]['idPriceList'] = $findPrice['id_price_list'];

        $findCustomPrice = $generalCustomPricesDao->findCustomPrice($customPrices[$i], $id_company);

        if (!$findCustomPrice) {
            $resolution = $customPricesDao->insertCustomPricesByCompany($customPrices[$i], $id_company);
        } else {
            $customPrices[$i]['idCustomPrice'] = $findCustomPrice['id_custom_price'];
            $resolution = $customPricesDao->updateCustomPrice($customPrices[$i]);
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Precios personalizados importados correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateCustomPrice', function (Request $request, Response $response, $args) use (
    $customPricesDao,
    $autenticationDao,
    $generalCustomPricesDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    session_start();
    $id_company = $_SESSION['id_company'];
    $dataCustomPrice = $request->getParsedBody();
    $data = [];

    $findPrice = $generalCustomPricesDao->findCustomPrice($dataCustomPrice, $id_company);

    !is_array($findPrice) ? $data['id_custom_price'] = 0 : $data = $findPrice;

    if ($data['id_custom_price'] == $dataCustomPrice['idCustomPrice'] || $data['id_custom_price'] == 0) {
        $customPrices = $customPricesDao->updateCustomPrice($dataCustomPrice);

        if ($customPrices == null)
            $resp = array('success' => true, 'message' => 'Precio modificado correctamente');
        else if (isset($customPrices['info']))
            $resp = array('info' => true, 'message' => $customPrices['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Producto con lista de precio ya existente. Ingrese un nuevo precio');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteCustomPrice/{id_custom_price}', function (Request $request, Response $response, $args) use (
    $customPricesDao,
    $autenticationDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $customPrices = $customPricesDao->deleteCustomPrice($args['id_custom_price']);

    if ($customPrices == null)
        $resp = array('success' => true, 'message' => 'Precio eliminado correctamente');
    else if (isset($customPrices['info']))
        $resp = array('info' => true, 'message' => $customPrices['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
