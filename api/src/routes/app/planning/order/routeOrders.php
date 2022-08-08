<?php

use tezlikv3\dao\ClientsDao;
use tezlikv3\dao\DeliveryDateDao;
use tezlikv3\dao\MallasDao;
use tezlikv3\dao\OrdersDao;
use tezlikv3\dao\PlanProductsDao;

$ordersDao = new OrdersDao();
$productsDao = new PlanProductsDao();
$clientsDao = new ClientsDao();
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

$app->post('/orderDataValidation', function (Request $request, Response $response, $args) use ($ordersDao, $productsDao, $clientsDao) {
    $dataOrder = $request->getParsedBody();

    if (isset($dataOrder)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;
        $order = $dataOrder['importOrder'];

        for ($i = 0; $i < sizeof($order); $i++) {
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
                $client = $clientsDao->insertClient($order[$i], $id_company);
                $order[$i]['idClient'] = $client['id_client'];
            } else $order[$i]['idClient'] = $findClient['id_client'];

            if (
                empty($order[$i]['order'])  || empty($order[$i]['dateOrder']) || empty($order[$i]['minDate']) ||
                empty($order[$i]['maxDate']) || empty($order[$i]['originalQuantity']) ||  empty($order[$i]['quantity'])
            ) {
                $i = $i + 1;
                $dataImportOrder = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            } else {
                $findOrder = $ordersDao->findOrder($order[$i], $id_company);
                !$findOrder ? $insert = $insert + 1 : $update = $update + 1;
                $dataImportOrder['insert'] = $insert;
                $dataImportOrder['update'] = $update;
            }
        }
    } else $dataImportOrder = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportOrder, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addOrder', function (Request $request, Response $response, $args) use ($ordersDao, $productsDao, $clientsDao, $deliveryDateDao, $mallasDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOrder = $request->getParsedBody();

    $order = $dataOrder['importOrder'];

    for ($i = 0; $i < sizeof($order); $i++) {
        // Obtener id producto
        $findProduct = $productsDao->findProduct($order[$i], $id_company);
        $order[$i]['idProduct'] = $findProduct['id_product'];

        // Obtener id cliente
        $findClient = $clientsDao->findClient($order[$i], $id_company);
        $order[$i]['idClient'] = $findClient['id_client'];

        // Consultar pedido
        $findOrder = $ordersDao->findOrder($order[$i], $id_company);
        if (!$findOrder) $resolution = $ordersDao->insertOrderByCompany($order[$i], $id_company);
        else {
            $order[$i]['idOrder'] = $findOrder['id_order'];
            $resolution = $ordersDao->updateOrder($order[$i]);
        }

        // Consultar malla cliente
        $findMalla = $mallasDao->findMalla($order[$i]);
        if (!$findMalla) $resolution = $mallasDao->insertMallaCliente($order[$i]);
        else {
            $order[$i]['idMalla']  = $findMalla['id'];
            $resolution = $mallasDao->updateMallaCliente($order[$i]);
        }
        // Calcular fecha entrega
        $deliveryDateDao->calcDeliveryDate($order[$i]);

        //Obtener todos los pedidos
        $data[$i] = $order[$i]['order'];
    }

    // Cambiar estado pedidos
    $changeStatus = $ordersDao->changeStatus($data);

    if ($resolution == null && $changeStatus == null) $resp = array('success' => true, 'message' => 'Pedido importado correctamente');
    else $resp = array('error' => true, 'message' => 'Ocurrio un error al importar el pedido. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
