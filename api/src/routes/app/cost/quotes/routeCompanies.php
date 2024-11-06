<?php

use tezlikv3\dao\GeneralQuotesDao;
use tezlikv3\dao\FilesDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\QCompaniesDao;
use tezlikv3\dao\WebTokenDao;

$companiesDao = new QCompaniesDao();
$webTokenDao = new WebTokenDao();
$lastDataDao = new LastDataDao();
$generalQuotesDao = new GeneralQuotesDao();
$FilesDao = new FilesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/quotesCompanies', function (Request $request, Response $response, $args) use (
    $companiesDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $companies = $companiesDao->findAllCompanies($id_company);
    $response->getBody()->write(json_encode($companies, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addQCompany', function (Request $request, Response $response, $args) use (
    $companiesDao,
    $webTokenDao,
    $lastDataDao,
    $FilesDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $dataCompany = $request->getParsedBody();

    if (
        empty($dataCompany['nit']) || empty($dataCompany['companyName']) || empty($dataCompany['address']) ||
        empty($dataCompany['phone']) || empty($dataCompany['city'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $companies = $companiesDao->insertCompany($dataCompany, $id_company);

        if (sizeof($_FILES) > 0) {
            $lastCompany = $lastDataDao->findLastInsertedQCompany();

            // Insertar imagen
            $FilesDao->imageQCompany($lastCompany['id_quote_company'], $id_company);
        }

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

$app->post('/updateQCompany', function (Request $request, Response $response, $args) use (
    $companiesDao,
    $webTokenDao,
    $FilesDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $dataCompany = $request->getParsedBody();

    if (
        empty($dataCompany['idCompany']) || empty($dataCompany['nit']) || empty($dataCompany['companyName']) ||
        empty($dataCompany['address']) || empty($dataCompany['phone']) || empty($dataCompany['city'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $companies = $companiesDao->updateCompany($dataCompany);

        if (sizeof($_FILES) > 0)
            $FilesDao->imageQCompany($dataCompany['idCompany'], $id_company);

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

$app->get('/deleteQCompany/{id_company}', function (Request $request, Response $response, $args) use (
    $companiesDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

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
