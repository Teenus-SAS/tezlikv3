<?php

namespace tezlikv3\Dao;

use tezlikv3\dao\DataHistoricalDao;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/dataHistorical', function (Request $request, Response $response, $args) {

    try {
        $dataHistoricalDao = new DataHistoricalDao();

        $id_company = $_SESSION['id_company'];
        $expenses = $dataHistoricalDao->getHistoricalExpenses($id_company);
        $distribution = $dataHistoricalDao->getHistoricalDistribution($id_company);
        $products = $dataHistoricalDao->getHistoricalProducts($id_company);

        $result = [$expenses, $distribution, $products];

        return ResponseHelper::withJson($response, $result, 200);
    } catch (\Exception $e) {
        error_log("Error al obtener dataHistoric: " . $e->getMessage());
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Error al obtener data Historic'], 500);
    }
})->add(new SessionMiddleware());
