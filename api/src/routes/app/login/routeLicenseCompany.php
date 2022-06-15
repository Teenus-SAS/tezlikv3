<?php

use tezlikv2\dao\LicenseCompanyDao;

$licenseCompanyDao = new LicenseCompanyDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Licencia compañia */

$app->post('/addLicenseCompany', function (Request $request, Response $response, $args) use ($licenseCompanyDao) {
    session_start();
    $dataLicenseCompany = $request->getParsedBody();
    $id_company = $dataLicenseCompany['id_company'];

    if (empty($dataLicenseCompany['licenseStart']) || empty($dataLicenseCompany['quantityUser']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {
        $licenseCompany = $licenseCompanyDao->insertLicenseCompanyByCompany($dataLicenseCompany, $id_company);
        if ($licenseCompany == null)
            $resp = array('success' => true, 'message' => 'Licencia de compañia creada correctamente');
        /*else if ($licenseCompany == 2)
            $resp = array('error' => true, 'message' => 'Ingrese campo `license_start`');*/
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateLicenseCompany', function (Request $request, Response $response, $args) use ($licenseCompanyDao) {
    $dataLicenseCompany = $request->getParsedBody();

    if (empty($dataLicenseCompany['licenseStart']) || empty($dataLicenseCompany['quantityUser']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $licenseCompany = $licenseCompanyDao->updateLicenseCompany($dataLicenseCompany);
        if ($licenseCompany == null)
            $resp = array('success' => true, 'message' => 'Licencia de compañia actualizada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteLicenseCompany/{id_company_license}', function (Request $request, Response $response, $args) use ($licenseCompanyDao) {
    $licenseCompany = $licenseCompanyDao->deleteLicenseCompany($args['id_company_license']);

    if ($licenseCompany == null)
        $resp = array('success' => true, 'message' => 'Licencia de compañia eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la licencia de compañia, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
