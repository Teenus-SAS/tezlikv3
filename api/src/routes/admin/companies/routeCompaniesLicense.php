<?php

use tezlikv2\dao\CompaniesLicenseDao;
use tezlikv2\dao\CompaniesLicenseStatusDao;

$companiesLicenseDao = new CompaniesLicenseDao();
$companiesLicenseStatusDao = new CompaniesLicenseStatusDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


//Nombre de empresa y datos de licencia
$app->get('/licenses', function (Request $request, Response $response, $args) use ($companiesLicenseDao) {
    $resp = $companiesLicenseDao->findCompanyLicenseActive();
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

//Actualizar fechas de licencia y cantidad de usuarios
$app->post('/updateLicense', function (Request $request, Response $response, $args) use ($companiesLicenseDao) {
    $dataLicense = $request->getParsedBody();
    $license = $companiesLicenseDao->updateLicense($dataLicense);

    if ($license == null) {
        $resp = array('success' => true, 'message' => 'Licencia actaulizada correctamente');
    } else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error al actualizar la licencia. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

//Cambiar estado licencia
$app->post('/changeStatusCompany/{id_company}', function (Request $request, Response $response, $args) use ($companiesLicenseStatusDao) {
    $sts = $companiesLicenseStatusDao->status($args['id_company']);
    $status = $sts['status'];

    if ($status == 1) {
        $licStatus = $companiesLicenseStatusDao->statusLicense(0, $args['id_company']);

        if ($licStatus == null) {
            $resp = array('success' => true, 'message' => 'Inactivo');
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');
        }
        
    }

    if ($status == 0) {
        $licStatus = $companiesLicenseStatusDao->statusLicense(1, $args['id_company']);

        if ($licStatus == null) {
            $resp = array('success' => true, 'message' => 'Activo');
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');
        }
        
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
