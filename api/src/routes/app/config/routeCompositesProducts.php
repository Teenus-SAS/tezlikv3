<?php

use tezlikv3\dao\{
    CompositeProductsDao,
    CostCompositeProductsDao,
    CostMaterialsDao,
    CostWorkforceDao,
    GeneralCompositeProductsDao,
    GeneralProductsDao,
    IndirectCostDao,
    PriceProductDao,
    PriceUSDDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/subproducts', function (RouteCollectorProxy $group) {

    $group->get('/compositeProducts/{id_product}', function (Request $request, Response $response, $args) {

        // session_start();
        $id_company = $_SESSION['id_company'];

        $compositeProductsDao = new CompositeProductsDao();

        $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($args['id_product'], $id_company);
        $response->getBody()->write(json_encode($compositeProducts));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/allCompositeProducts', function (Request $request, Response $response, $args) {

        // session_start();
        $id_company = $_SESSION['id_company'];

        $generalCompositeProductsDao = new GeneralCompositeProductsDao();

        $compositeProducts = $generalCompositeProductsDao->findAllCompositeProductsByCompany($id_company);
        $response->getBody()->write(json_encode($compositeProducts));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/addCompositeProduct', function (Request $request, Response $response, $args) {
        // session_start();
        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $dataProduct = $request->getParsedBody();

        $compositeProductsDao = new CompositeProductsDao();
        $generalCompositeProductsDao = new GeneralCompositeProductsDao();
        $costMaterialsDao = new CostMaterialsDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $generalProductsDao = new GeneralProductsDao();
        $costWorkforceDao = new CostWorkforceDao();
        $indirectCostDao = new IndirectCostDao();

        $composite = $generalCompositeProductsDao->findCompositeProduct($dataProduct);

        if (!$composite) {
            $resolution = $compositeProductsDao->insertCompositeProductByCompany($dataProduct, $id_company);

            if ($resolution == null) {
                /* Calcular costo indirecto */
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProduct['idProduct'], $id_company);
                // Cambiar a 0
                $indirectCostDao->updateCostIndirectCostByProduct(0, $dataProduct['idProduct']);
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

                if (isset($product['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($dataProduct['idProduct'], $product['totalPrice']);
            }

            // Convertir a Dolares 
            if ($resolution == null && isset($product) && $_SESSION['flag_currency_usd'] == '1') {
                $k = [];
                $k['price'] = $product['totalPrice'];
                $k['sale_price'] = $product['sale_price'];
                $k['id_product'] = $dataProduct['idProduct'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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

                    if (isset($data['totalPrice']))
                        $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                    if (isset($resolution['info'])) break;
                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $arr['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }
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

    $group->post('/updateCompositeProduct', function (Request $request, Response $response, $args) {
        // session_start();
        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $dataProduct = $request->getParsedBody();
        $data = [];

        $compositeProductsDao = new CompositeProductsDao();
        $generalCompositeProductsDao = new GeneralCompositeProductsDao();
        $costMaterialsDao = new CostMaterialsDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $generalProductsDao = new GeneralProductsDao();
        $costWorkforceDao = new CostWorkforceDao();
        $indirectCostDao = new IndirectCostDao();

        $composite = $generalCompositeProductsDao->findCompositeProduct($dataProduct);

        !is_array($composite) ? $data['id_composite_product'] = 0 : $data = $composite;

        if ($data['id_composite_product'] == $dataProduct['idCompositeProduct'] || $data['id_composite_product'] == 0) {
            $resolution = $compositeProductsDao->updateCompositeProduct($dataProduct);

            if ($resolution == null) {
                /* Calcular costo indirecto */
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProduct['idProduct'], $id_company);
                // Cambiar a 0
                $indirectCostDao->updateCostIndirectCostByProduct(0, $dataProduct['idProduct']);
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

                if (isset($resolution['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($dataProduct['idProduct'], $product['totalPrice']);
            }

            // Convertir a Dolares 
            if ($resolution == null && isset($product) && $_SESSION['flag_currency_usd'] == '1') {
                $k = [];
                $k['price'] = $product['totalPrice'];
                $k['sale_price'] = $product['sale_price'];
                $k['id_product'] = $dataProduct['idProduct'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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

                    if (isset($data['totalPrice']))
                        $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                    if (isset($resolution['info'])) break;

                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $arr['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }
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

    $group->post('/deleteCompositeProduct', function (Request $request, Response $response, $args) {
        // session_start();
        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $dataProduct = $request->getParsedBody();

        $compositeProductsDao = new CompositeProductsDao();
        $generalCompositeProductsDao = new GeneralCompositeProductsDao();
        $costMaterialsDao = new CostMaterialsDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $generalProductsDao = new GeneralProductsDao();
        $costWorkforceDao = new CostWorkforceDao();
        $indirectCostDao = new IndirectCostDao();

        $resolution = $compositeProductsDao->deleteCompositeProduct($dataProduct['idCompositeProduct']);

        if ($resolution == null) {
            /* Calcular costo indirecto */
            // Buscar la maquina asociada al producto
            $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProduct['idProduct'], $id_company);
            // Cambiar a 0
            $indirectCostDao->updateCostIndirectCostByProduct(0, $dataProduct['idProduct']);
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

            if (isset($product['totalPrice']))
                $resolution = $generalProductsDao->updatePrice($dataProduct['idProduct'], $product['totalPrice']);
        }

        // Convertir a Dolares 
        if ($resolution == null && isset($product) && $_SESSION['flag_currency_usd'] == '1') {
            $k = [];
            $k['price'] = $product['totalPrice'];
            $k['sale_price'] = $product['sale_price'];
            $k['id_product'] = $dataProduct['idProduct'];

            $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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

                if (isset($data['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                if (isset($resolution['info'])) break;
                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $data['totalPrice'];
                    $k['sale_price'] = $data['sale_price'];
                    $k['id_product'] = $arr['id_product'];

                    $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                }
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
})->add(new SessionMiddleware());
