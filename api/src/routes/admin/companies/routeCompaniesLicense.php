<?php

use tezlikv3\dao\CompaniesLicenseDao;
use tezlikv3\dao\CompaniesLicenseStatusDao;
use tezlikv3\dao\WebTokenDao;

$companiesLicenseDao = new CompaniesLicenseDao();
$webTokenDao = new WebTokenDao();
$companiesLicenseStatusDao = new CompaniesLicenseStatusDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


//Nombre de empresa y datos de licencia
$app->get('/licenses', function (Request $request, Response $response, $args) use (
    $companiesLicenseDao,
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

    $resp = $companiesLicenseDao->findCompanyLicenseActive();
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/addLicense', function (Request $request, Response $response, $args) use (
    $companiesLicenseDao,
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

    $dataLicense = $request->getParsedBody();
    empty($dataLicense['plan']) ? $dataLicense['plan'] = 4 : $dataLicense['plan'];

    $license = $companiesLicenseDao->addLicense($dataLicense, $dataLicense['company'], 1);

    if ($license == null)
        $resp = array('success' => true, 'message' => 'Licencia ingresada correctamente');
    else if ($license['info'])
        $resp = array('info' => true, 'message' => $license['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al actualizar la licencia. Intente nuevamente');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

//Actualizar fechas de licencia y cantidad de usuarios
$app->post('/updateLicense', function (Request $request, Response $response, $args) use (
    $companiesLicenseDao,
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

    $dataLicense = $request->getParsedBody();
    $license = $companiesLicenseDao->updateLicense($dataLicense);

    if ($license == null) {
        $resp = array('success' => true, 'message' => 'Licencia actualizada correctamente');
    } else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error al actualizar la licencia. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

//Cambiar estado licencia
$app->post('/changeStatusCompany/{id_company}', function (Request $request, Response $response, $args) use (
    $companiesLicenseStatusDao,
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

    $sts = $companiesLicenseStatusDao->status($args['id_company']);
    $status = $sts['license_status'];

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
