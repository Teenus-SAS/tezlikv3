<?php

use tezlikv3\dao\CompaniesDao;
use tezlikv3\dao\CompaniesLicenseDao;
use tezlikv3\dao\ImageDao;

$companiesDao = new CompaniesDao();
$imageDao = new ImageDao();
$companiesLicDao = new CompaniesLicenseDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/allCompanies', function (Request $request, Response $response, $args) use ($companiesDao) {
    $resp = $companiesDao->findAllCompanies();
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

//Datos de empresas activas
$app->get('/companies/{stat}', function (Request $request, Response $response, $args) use ($companiesDao) {
    $resp = $companiesDao->findAllCompaniesLicenses($args['stat']);
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

//Nueva Empresa
$app->post('/addNewCompany', function (Request $request, Response $response, $args) use ($companiesDao, $imageDao, $companiesLicDao) {
    $dataCompany = $request->getParsedBody();
    /*Agregar datos a companies */
    $company = $companiesDao->addCompany($dataCompany);

    $lastId = $companiesDao->findLastCompany();
    if (sizeof($_FILES) > 0) {
        $imageDao->logoCompany($lastId['idCompany']);
    }
    /*Agregar datos a companies licenses*/
    $company = $companiesLicDao->addLicense($dataCompany, $lastId['idCompany']);


    if ($company == null)
        $resp = array('success' => true, 'message' => 'Datos de Empresa agregados correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al ingresar la informacion. Intente nuevamente');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


//Actualizar Empresa
$app->post('/updateDataCompany', function (Request $request, Response $response, $args) use ($companiesDao, $imageDao) {
    $dataCompany = $request->getParsedBody();
    $company = $companiesDao->updateCompany($dataCompany);

    if (sizeof($_FILES) > 0)
        $imageDao->logoCompany($dataCompany['idCompany']);

    if ($company == null) {
        $resp = array('success' => true, 'message' => 'Datos de Empresa actualizados correctamente');
    } else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error al actualizar la licencia. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
