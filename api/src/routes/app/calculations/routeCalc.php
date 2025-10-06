<?php

use tezlikv3\dao\{
    CostWorkforceDao,
    GeneralProductsDao,
    GeneralProductsProcessDao,
    IndirectCostDao,
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

// Grupo: Endpoints de Cálculos de Costos
$app->group('/calculations', function (RouteCollectorProxy $group) {

    $group->get('/calcGeneralWorkforce', function (Request $request, Response $response, $args) {

        // Instancias internas específicas para este endpoint
        $generalProductsProcessDao = new GeneralProductsProcessDao();
        $costWorkforceDao = new CostWorkforceDao();

        $id_company = $_SESSION['id_company'];
        $productProcess = $generalProductsProcessDao->findAllProductsprocess($id_company);

        for ($i = 0; $i < sizeof($productProcess); $i++) {
            if (isset($resolution['info'])) break;

            if ($productProcess[$i]['auto_machine'] == 'NO') {
                if ($productProcess[$i]['employee'] == '' || $_SESSION['flag_employee'] == 0) {
                    if ($_SESSION['inyection'] == 1)
                        $resolution = $costWorkforceDao->calcCostPayrollInyection($productProcess[$i]['id_product'], $id_company);
                    else
                        $resolution = $costWorkforceDao->calcCostPayroll($productProcess[$i]['id_product'], $id_company);
                } else {
                    if ($_SESSION['inyection'] == 1)
                        $resolution = $costWorkforceDao->calcCostPayrollInyectionGroupEmployee($productProcess[$i]['id_product'], $productProcess[$i]['employee']);
                    else {
                        $resolution = $costWorkforceDao->calcCostPayrollGroupByEmployee($productProcess[$i]['id_product'], $id_company, $productProcess[$i]['employee']);
                    }
                }
            } else {
                $resolution = $costWorkforceDao->calcCostPayroll($productProcess[$i]['id_product'], $id_company);
                $resolution = $generalProductsProcessDao->updateEmployees($productProcess[$i]['id_product_process'], '');
            }

            // Calcular costo nomina total
            if (isset($resolution['info'])) break;
            $dataPayroll = $costWorkforceDao->sumTotalCostPayroll($productProcess[$i]['id_product'], $id_company);

            $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $productProcess[$i]['id_product'], $id_company);
        }

        $resp = array('success' => true, 'message' => 'Calculo de mano de obra actualizados correctamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });


    $group->get('/calcAllIndirectCost', function (Request $request, Response $response, $args) {

        // Instancias internas específicas para este endpoint
        $productsDao = new GeneralProductsDao();
        $indirectCostDao = new IndirectCostDao();

        // session_start();
        $id_company = $_SESSION['id_company'];

        $products = $productsDao->findAllProductsByCRM($id_company);

        $resolution = null;

        foreach ($products as $arr) {
            // Buscar la maquina asociada al producto
            $dataProductMachine = $indirectCostDao->findMachineByProduct($arr['id_product'], $id_company);
            // Cambiar a 0
            $indirectCostDao->updateCostIndirectCostByProduct(0, $arr['id_product']);
            // Calcular costo indirecto
            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // Actualizar campo
            $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

            if (isset($resolution['info'])) break;
        }

        if ($resolution == null)
            $resp = ['success' => true, 'message' => 'costos indirectos calculados correctamente en todos los productos'];
        else if (isset($resolution['info']))
            $resp = ['info' => true, 'message' => $resolution['message']];
        else
            $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la informacion. Intente nuevamente'];

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
