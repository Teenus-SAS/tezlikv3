<?php

use tezlikv3\dao\QCompaniesDao;

$companiesDao = new QCompaniesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/quotesCompanies', function (Request $request, Response $response, $args) use ($companiesDao) {
    $companies = $companiesDao->findAllCompanies();
    $response->getBody()->write(json_encode($companies, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addQCompany', function (Request $request, Response $response, $args) use ($companiesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataCompany = $request->getParsedBody();

    if (
        empty($dataCompany['nit']) || empty($dataCompany['companyName']) || empty($dataCompany['address']) ||
        empty($dataCompany['phone']) || empty($dataCompany['city'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $companies = $companiesDao->insertCompany($dataCompany, $id_company);

        if ($companies == null)
            $resp = array('success' => true, 'message' => 'Compañia ingresada correctamente');
        else if (isset($companies['info']))
            $resp = array('info' => true, 'message' => $companies['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateQCompany', function (Request $request, Response $response, $args) use ($companiesDao) {
    $dataCompany = $request->getParsedBody();

    if (
        empty($dataCompany['idCompany']) || empty($dataCompany['nit']) || empty($dataCompany['companyName']) ||
        empty($dataCompany['address']) || empty($dataCompany['phone']) || empty($dataCompany['city'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $companies = $companiesDao->updateCompany($dataCompany);

        if ($companies == null)
            $resp = array('success' => true, 'message' => 'Compañia modificada correctamente');
        else if (isset($companies['info']))
            $resp = array('info' => true, 'message' => $companies['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteQCompany/{id_company}', function (Request $request, Response $response, $args) use ($companiesDao) {
    $companies = $companiesDao->deleteCompany($args['id_company']);

    if ($companies == null)
        $resp = array('success' => true, 'message' => 'Compañia eliminada correctamente');
    else if (isset($companies['info']))
        $resp = array('info' => true, 'message' => $companies['message']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar la información');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});