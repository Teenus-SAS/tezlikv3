<?php

use tezlikv3\dao\CompositeProductsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\PriceProductDao;

$compositeProductsDao = new CompositeProductsDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$priceProductDao = new PriceProductDao();
$generalProductsDao = new GeneralProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/compositeProducts/{id_product}', function (Request $request, Response $response, $args) use ($compositeProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($args['id_product'], $id_company);
    $response->getBody()->write(json_encode($compositeProducts));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addCompositeProduct', function (Request $request, Response $response, $args) use (
    $compositeProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $priceProductDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    $composite = $generalCompositeProductsDao->findCompositeProduct($dataProduct);

    if (!$composite) {
        $resolution = $compositeProductsDao->insertCompositeProductByCompany($dataProduct, $id_company);

        // Calcular costo materia prima compuesta
        if ($resolution == null) {
            $dataProduct = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($dataProduct);
            $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($dataProduct);
        }

        // Calcular costo materia prima
        if ($resolution == null) {
            $dataProduct = $costMaterialsDao->calcCostMaterialByCompositeProduct($dataProduct);
            $resolution = $costMaterialsDao->updateCostMaterials($dataProduct, $id_company);
        }

        // Calcular precio producto
        if ($resolution == null) {
            $product = $priceProductDao->calcPrice($dataProduct['idProduct']);
            $resolution = $generalProductsDao->updatePrice($dataProduct['idProduct'], $product['totalPrice']);
        }

        if ($resolution == null) {
            $resp = array('success' => true, 'message' => 'Producto compuesto agregado correctamente');
        } else if (isset($resolution['info'])) {
            $resp = array('info' => true, 'message' => $resolution['message']);
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente');
        }
    } else {
        $resp = array('error' => true, 'message' => 'Producto compuesto ya existe en la base de datos.');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateCompositeProduct', function (Request $request, Response $response, $args) use (
    $compositeProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $priceProductDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();
    $data = [];

    $composite = $generalCompositeProductsDao->findCompositeProduct($dataProduct);

    !is_array($composite) ? $data['id_composite_product'] = 0 : $data = $composite;

    if ($data['id_composite_product'] == $dataProduct['idCompositeProduct'] || $data['id_composite_product'] == 0) {
        $resolution = $compositeProductsDao->updateCompositeProduct($dataProduct);

        // Calcular costo materia prima compuesta
        if ($resolution == null) {
            $dataProduct = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($dataProduct);
            $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($dataProduct);
        }

        // Calcular costo materia prima
        if ($resolution == null) {
            $dataProduct = $costMaterialsDao->calcCostMaterialByCompositeProduct($dataProduct);
            $resolution = $costMaterialsDao->updateCostMaterials($dataProduct, $id_company);
        }

        // Calcular precio producto
        if ($resolution == null) {
            $product = $priceProductDao->calcPrice($dataProduct['idProduct']);
            $resolution = $generalProductsDao->updatePrice($dataProduct['idProduct'], $product['totalPrice']);
        }

        if ($resolution == null) {
            $resp = array('success' => true, 'message' => 'Producto compuesto modificado correctamente');
        } else if (isset($resolution['info'])) {
            $resp = array('info' => true, 'message' => $resolution['message']);
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente');
        }
    } else {
        $resp = array('error' => true, 'message' => 'Producto compuesto ya existe en la base de datos.');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteCompositeProduct', function (Request $request, Response $response, $args) use (
    $compositeProductsDao,
    $costMaterialsDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    $resolution = $compositeProductsDao->deleteCompositeProduct($dataProduct['idCompositeProduct']);

    // Calcular costo materia prima compuesta
    if ($resolution == null) {
        $dataProduct = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($dataProduct);
        $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($dataProduct);
    }

    // Calcular costo materia prima
    if ($resolution == null) {
        $dataProduct = $costMaterialsDao->calcCostMaterialByCompositeProduct($dataProduct);
        $resolution = $costMaterialsDao->updateCostMaterials($dataProduct, $id_company);
    }

    // Calcular precio producto
    if ($resolution == null) {
        $product = $priceProductDao->calcPrice($dataProduct['idProduct']);
        $resolution = $generalProductsDao->updatePrice($dataProduct['idProduct'], $product['totalPrice']);
    }

    if ($resolution == null) {
        $resp = array('success' => true, 'message' => 'Producto compuesto eliminado correctamente');
    } else if (isset($resolution['info'])) {
        $resp = array('info' => true, 'message' => $resolution['message']);
    } else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error al eliminar la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
