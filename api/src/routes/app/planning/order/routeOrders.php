<?php

use tezlikv3\dao\ClientsDao;
use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\DeliveryDateDao;
use tezlikv3\dao\GeneralOrdersDao;
use tezlikv3\dao\MallasDao;
use tezlikv3\dao\OrdersDao;
use tezlikv3\dao\OrderTypesDao;
use tezlikv3\dao\PlanProductsDao;

$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new PlanProductsDao();
$clientsDao = new ClientsDao();
$orderTypesDao = new OrderTypesDao();
$mallasDao = new MallasDao();
$deliveryDateDao = new DeliveryDateDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/orders', function (Request $request, Response $response, $args) use ($ordersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $orders = $ordersDao->findAllOrdersByCompany($id_company);
    $response->getBody()->write(json_encode($orders, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/orderDataValidation', function (Request $request, Response $response, $args) use (
    $ordersDao,
    $productsDao,
    $clientsDao,
    $orderTypesDao
) {
    $dataOrder = $request->getParsedBody();

    if (isset($dataOrder)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;
        $order = $dataOrder['importOrder'];

        for ($i = 0; $i < sizeof($order); $i++) {
            if (
                empty($order[$i]['order'])  || empty($order[$i]['dateOrder']) || empty($order[$i]['minDate']) ||
                empty($order[$i]['maxDate']) || empty($order[$i]['originalQuantity']) ||  empty($order[$i]['quantity'])
            ) {
                $i = $i + 1;
                $dataImportOrder = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($order[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 1;
                $dataImportOrder = array('error' => true, 'message' => "Producto no existe en la base de datos.<br>Fila: {$i}");
                break;
            } else $order[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id cliente
            $findClient = $clientsDao->findClient($order[$i], $id_company);
            if (!$findClient) {
                // Crear cliente
                $clientsDao->insertClient($order[$i], $id_company);
                $client = $clientsDao->findClient($order[$i], $id_company);
                $order[$i]['idClient'] = $client['id_client'];
            } else $order[$i]['idClient'] = $findClient['id_client'];

            // Obtener id Tipo pedido
            $findOrderType = $orderTypesDao->findOrderType($order[$i]);
            if (!$findOrderType) {
                $i = $i + 1;
                $dataImportOrder = array('error' => true, 'message' => "Tipo de pedido no existe en la base de datos.<br>Fila: {$i}");
                break;
            } else $order[$i]['idOrderType'] = $findOrderType['id_order_type'];

            $findOrder = $ordersDao->findOrder($order[$i], $id_company);
            !$findOrder ? $insert = $insert + 1 : $update = $update + 1;
            $dataImportOrder['insert'] = $insert;
            $dataImportOrder['update'] = $update;
        }
    } else $dataImportOrder = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportOrder, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addOrder', function (Request $request, Response $response, $args) use (
    $ordersDao,
    $generalOrdersDao,
    $convertDataDao,
    $productsDao,
    $clientsDao,
    $orderTypesDao,
    $deliveryDateDao,
    $mallasDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOrder = $request->getParsedBody();

    $dataOrders = sizeof($dataOrder);

    if ($dataOrders > 1) {
        $dataOrder = $convertDataDao->changeDateOrder($dataOrder);

        $order = $ordersDao->insertOrderByCompany($dataOrder, $id_company);

        $data[0] = $dataOrder['order'] . '-' . $dataOrder['idProduct'];

        if ($order == null)
            $resp = array('success' => true, 'message' => 'Pedido ingresado correctamente');
        else if (isset($order['info']))
            $resp = array('info' => true, 'message' => $order['info']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $order = $dataOrder['importOrder'];

        for ($i = 0; $i < sizeof($order); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($order[$i], $id_company);
            $order[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id cliente
            $findClient = $clientsDao->findClient($order[$i], $id_company);
            $order[$i]['idClient'] = $findClient['id_client'];

            // Obtener id tipo pedido
            $findOrderType = $orderTypesDao->findOrderType($order[$i]);
            $order[$i]['idOrderType'] = $findOrderType['id_order_type'];

            $order[$i] = $convertDataDao->changeDateOrder($order[$i]);

            // Consultar pedido
            $findOrder = $ordersDao->findOrder($order[$i], $id_company);
            if (!$findOrder) $resolution = $ordersDao->insertOrderByCompany($order[$i], $id_company);
            else {
                $order[$i]['idOrder'] = $findOrder['id_order'];
                $resolution = $ordersDao->updateOrder($order[$i]);
            }
            // Obtener todos los pedidos
            $data[$i] = $order[$i]['order'] . '-' . $order[$i]['idProduct'];
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Pedido importado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al importar el pedido. Intente nuevamente');
    }

    // Cambiar estado pedidos
    $result = $generalOrdersDao->findAllOrdersConcat($id_company);

    $arrayBD = [];
    for ($i = 0; $i < sizeof($result); $i++) {
        array_push($arrayBD, $result[$i]['concate']);
    }

    $tam_arrayBD = sizeof($arrayBD);
    $tam_result = sizeof($data);

    if ($tam_arrayBD > $tam_result)
        $array_diff = array_diff($arrayBD, $data);
    else
        $array_diff = array_diff($data, $arrayBD);

    //reindezar array
    $array_diff = array_values($array_diff);

    if ($array_diff)
        for ($i = 0; $i < sizeof($array_diff); $i++) {
            $posicion =  strrpos($array_diff[$i], '-');
            $id_product = substr($array_diff[$i], $posicion + 1);
            $order = substr($array_diff[$i], 0, $posicion);
            $result = $generalOrdersDao->changeStatus($order, $id_product);
        }
    else if (sizeof($array_diff) == 0)
        $result = null;


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateOrder', function (Request $request, Response $response, $args) use (
    $ordersDao,
    $convertDataDao
) {
    $dataOrder = $request->getParsedBody();

    if (empty($dataOrder['order']) || empty($dataOrder['idProduct']) || empty($dataOrder['idClient']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $dataOrder = $convertDataDao->changeDateOrder($dataOrder);

        $order = $ordersDao->updateOrder($dataOrder);

        if ($order == null)
            $resp = array('success' => true, 'message' => 'Pedido modificado correctamente');
        else if ($order['info'])
            $resp = array('info' => true, 'message' => $order['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteOrder/{id_order}', function (Request $request, Response $response, $args) use ($ordersDao) {
    $order = $ordersDao->deleteOrder($args['id_order']);

    if ($order == null)
        $resp = array('success' => true, 'message' => 'Pedido eliminado correctamente');
    else if ($order['info'])
        $resp = array('info' => true, 'message' => $order['info']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar el pedido. Existe información asociada a el');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
