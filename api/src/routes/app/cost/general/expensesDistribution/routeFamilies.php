<?php

use tezlikv3\dao\FamiliesDao;
use tezlikv3\dao\GeneralExpenseDistributionDao;

$familiesDao = new FamiliesDao();
$generalExpenseDistributionDao = new GeneralExpenseDistributionDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/families', function (Request $request, Response $response, $args) use ($familiesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $families = $familiesDao->findAllFamiliesByCompany($id_company);
    $response->getBody()->write(json_encode($families, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expensesDistributionFamilies', function (Request $request, Response $response, $args) use ($familiesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $expensesDistribution = $familiesDao->findAllExpensesDistributionByCompany($id_company);
    $response->getBody()->write(json_encode($expensesDistribution, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expensesDistributionFamilies/{id_family}', function (Request $request, Response $response, $args) use ($generalExpenseDistributionDao) {
    $expensesDistribution = $generalExpenseDistributionDao->findAllExpensesDistributionByFamily($args['id_family']);
    $response->getBody()->write(json_encode($expensesDistribution, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expensesDistributionFamiliesProducts', function (Request $request, Response $response, $args) use ($familiesDao) {
    $products = $familiesDao->findAllProductsNotInEDistribution($args['id_family']);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/changeTypeExpenseDistribution/{flag}', function (Request $request, Response $response, $args) use ($familiesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $typeExpense = $familiesDao->updateFlagFamily($args['flag'], $id_company);

    $_SESSION['flag_expense_distribution'] = $args['flag'];

    if ($typeExpense == null)
        $resp = array('success' => true, 'message' => 'Se selecciono el tipo de distribucion correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addFamily', function (Request $request, Response $response, $args) use (
    $familiesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataFamily = $request->getParsedBody();

    $findFamily = $familiesDao->findFamily($dataFamily, $id_company);

    if (!$findFamily) {
        $families = $familiesDao->insertFamilyByCompany($dataFamily, $id_company);

        if ($families == null)
            $resp = array('success' => true, 'message' => 'Familia agregada correctamente');
        else if (isset($families['info']))
            $resp = array('info' => true, 'message' => $families['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Familia ya existente. Ingrese un nueva familia');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
