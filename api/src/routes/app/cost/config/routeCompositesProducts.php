<?php

use tezlikv3\dao\CompositeProductsDao;
use tezlikv3\dao\CostCompositeProductsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\PriceProductDao;

$compositeProductsDao = new CompositeProductsDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$priceProductDao = new PriceProductDao();
$generalProductsDao = new GeneralProductsDao();
$costCompositeProductsDao = new CostCompositeProductsDao();
$costWorkforceDao = new CostWorkforceDao();
$indirectCostDao = new IndirectCostDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/compositeProducts/{id_product}', function (Request $request, Response $response, $args) use ($compositeProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($args['id_product'], $id_company);
    $response->getBody()->write(json_encode($compositeProducts));
    return $response->withHeader('Content-Type', 'application/json');
});
$app->get('/allCompositeProducts', function (Request $request, Response $response, $args) use ($generalCompositeProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $compositeProducts = $generalCompositeProductsDao->findAllCompositeProductsByCompany($id_company);
    $response->getBody()->write(json_encode($compositeProducts));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addCompositeProduct', function (Request $request, Response $response, $args) use (
    $compositeProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $priceProductDao,
    $generalProductsDao,
    $costCompositeProductsDao,
    $indirectCostDao,
    $costWorkforceDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    $composite = $generalCompositeProductsDao->findCompositeProduct($dataProduct);

    if (!$composite) {
        $resolution = $compositeProductsDao->insertCompositeProductByCompany($dataProduct, $id_company);

        if ($resolution == null) {
            /* Calcular costo indirecto */
            // Buscar la maquina asociada al producto
            $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProduct['idProduct'], $id_company);
            // Calcular costo indirecto
            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // Actualizar campo
            $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProduct['idProduct'], $id_company);
        }


        if ($resolution == null) {
            // Calcular costo nomina total
            $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($dataProduct['idProduct'], $id_company);

            $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $dataProduct['idProduct'], $id_company);
        }


        // if ($resolution == null) {
        //     $data = $costCompositeProductsDao->calcCostCompositeProduct($dataProduct);
        //     $product = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $dataProduct['idProduct'], $id_company);
        //     $product = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $dataProduct['idProduct'], $id_company);
        // }

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
            $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProduct['idProduct']);

            foreach ($productsCompositer as $arr) {
                if (isset($resolution['info'])) break;
                $data = [];
                $data['compositeProduct'] = $arr['id_child_product'];
                $data['idProduct'] = $arr['id_product'];

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($resolution['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($resolution['info'])) break;

                $data = $priceProductDao->calcPrice($arr['id_product']);
                $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
            }
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
    $generalProductsDao,
    $costCompositeProductsDao,
    $costWorkforceDao,
    $indirectCostDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();
    $data = [];

    $composite = $generalCompositeProductsDao->findCompositeProduct($dataProduct);

    !is_array($composite) ? $data['id_composite_product'] = 0 : $data = $composite;

    if ($data['id_composite_product'] == $dataProduct['idCompositeProduct'] || $data['id_composite_product'] == 0) {
        $resolution = $compositeProductsDao->updateCompositeProduct($dataProduct);

        if ($resolution == null) {
            /* Calcular costo indirecto */
            // Buscar la maquina asociada al producto
            $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProduct['idProduct'], $id_company);
            // Calcular costo indirecto
            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // Actualizar campo
            $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProduct['idProduct'], $id_company);
        }

        if ($resolution == null) {
            // Calcular costo nomina total
            $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($dataProduct['idProduct'], $id_company);

            $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $dataProduct['idProduct'], $id_company);
        }

        // if ($resolution == null) {
        //     $data = $costCompositeProductsDao->calcCostCompositeProduct($dataProduct);
        //     $product = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $dataProduct['idProduct'], $id_company);
        //     $product = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $dataProduct['idProduct'], $id_company);
        // }

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
            $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProduct['idProduct']);

            foreach ($productsCompositer as $arr) {
                if (isset($resolution['info'])) break;
                $data = [];
                $data['compositeProduct'] = $arr['id_child_product'];
                $data['idProduct'] = $arr['id_product'];

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($resolution['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($resolution['info'])) break;

                $data = $priceProductDao->calcPrice($arr['id_product']);
                $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
            }
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
    $generalCompositeProductsDao,
    $costCompositeProductsDao,
    $costWorkforceDao,
    $indirectCostDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    $resolution = $compositeProductsDao->deleteCompositeProduct($dataProduct['idCompositeProduct']);

    if ($resolution == null) {
        /* Calcular costo indirecto */
        // Buscar la maquina asociada al producto
        $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProduct['idProduct'], $id_company);
        // Calcular costo indirecto
        $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
        // Actualizar campo
        $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProduct['idProduct'], $id_company);
    }

    if ($resolution == null) {
        // Calcular costo nomina total
        $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($dataProduct['idProduct'], $id_company);

        $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $dataProduct['idProduct'], $id_company);
    }

    // if ($resolution == null) {
    //     $data = $costCompositeProductsDao->calcCostCompositeProduct($dataProduct);
    //     $product = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $dataProduct['idProduct'], $id_company);
    //     $product = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $dataProduct['idProduct'], $id_company);
    // }

    // Calcular costo materia prima compuesta
    // if ($resolution == null) {
    //     $dataProduct = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($dataProduct);
    //     $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($dataProduct);
    // }

    // Calcular costo materia prima
    if ($resolution == null) {
        $dataProduct = $costMaterialsDao->calcCostMaterialByCompositeProduct($dataProduct);
        $resolution = $costMaterialsDao->updateCostMaterials($dataProduct, $id_company);
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
        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProduct['idProduct']);

        foreach ($productsCompositer as $arr) {
            if (isset($resolution['info'])) break;
            $data = [];
            $data['compositeProduct'] = $arr['id_child_product'];
            $data['idProduct'] = $arr['id_product'];

            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
            $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

            if (isset($resolution['info'])) break;
            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
            $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

            if (isset($resolution['info'])) break;

            $data = $priceProductDao->calcPrice($arr['id_product']);
            $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
        }
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
