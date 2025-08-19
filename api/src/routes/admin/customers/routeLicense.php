<?php

use tezlikv3\dao\{CompaniesLicenseDao, CompaniesLicenseStatusDao};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/licenses', function (RouteCollectorProxy $group) {

    $group->get('', function (Request $request, Response $response, $args) {

        $companiesLicenseDao = new CompaniesLicenseDao();
        $companiesLicenseStatusDao = new CompaniesLicenseStatusDao();

        $resp = $companiesLicenseDao->findCompanyLicenseActive();
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/addLicense', function (Request $request, Response $response, $args) {

        $companiesLicenseDao = new CompaniesLicenseDao();
        $companiesLicenseStatusDao = new CompaniesLicenseStatusDao();

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
    $group->post('/updateLicense', function (Request $request, Response $response, $args) {

        $companiesLicenseDao = new CompaniesLicenseDao();
        $companiesLicenseStatusDao = new CompaniesLicenseStatusDao();

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
    $group->post('/changeStatusCompany/{id_company}', function (Request $request, Response $response, $args) {

        $companiesLicenseDao = new CompaniesLicenseDao();
        $companiesLicenseStatusDao = new CompaniesLicenseStatusDao();

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
})->add(new SessionMiddleware());
