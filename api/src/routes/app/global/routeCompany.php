<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\CompaniesLicenseDao;
use tezlikv3\dao\CompanyDao;
use tezlikv3\dao\GeneralCompanyLicenseDao;

$companyDao = new CompanyDao();
$autenticationDao = new AutenticationUserDao();
$companiesLicenseDao = new CompaniesLicenseDao();
$generalCompanyLicenseDao = new GeneralCompanyLicenseDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/company', function (Request $request, Response $response, $args) use (
    $companyDao,
    $autenticationDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $company = $companyDao->findDataCompanyByCompany($id_company);
    $response->getBody()->write(json_encode($company, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/changeDateContract/{op}', function (Request $request, Response $response, $args) use (
    $companiesLicenseDao,
    $generalCompanyLicenseDao,
    $autenticationDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $company = [];
    if ($args['op'] == '1') {
        // session_start();
        $company[0]['id_company'] = $_SESSION['id_company'];
        date_default_timezone_set('America/Bogota');
        $date = date('Y-m-d H:i:s');
    } else {
        $company = $companiesLicenseDao->findCompanyLicenseActive();
        $date = null;
    }

    $contract = null;

    for ($i = 0; $i < sizeof($company); $i++) {
        $contract = $generalCompanyLicenseDao->changeDateContract($company[$i]['id_company'], $date);

        if (isset($contract['info'])) break;
    }

    if ($contract == null) {
        if ($args['op'] == 1) {
            $resp = array('success' => true, 'message' => 'Información guardada correctamente');
            $_SESSION['date_contract'] = $date;
        } else
            $resp = array('success' => true, 'message' => 'Información enviada correctamente');
    } else if (isset($contract['info']))
        $resp = array('error' => true, 'message' => $contract['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al ingresar la informacion. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
