<?php

use tezlikv3\dao\{
    CompaniesDao,
    CompaniesLicenseDao,
    FilesDao,
    LastDataDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/customers', function (RouteCollectorProxy $group) {

    $group->get('/all', function (Request $request, Response $response, $args) {

        $companiesDao = new CompaniesDao();

        $resp = $companiesDao->findAllCompanies();
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    //Datos de empresas activas
    $group->get('/{stat}', function (Request $request, Response $response, $args) {

        $companiesDao = new CompaniesDao();

        $resp = $companiesDao->findAllCompaniesLicenses($args['stat']);
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    //Nueva Empresa
    $group->post('/addNewCompany', function (Request $request, Response $response, $args) {

        $companiesDao = new CompaniesDao();
        $FilesDao = new FilesDao();
        $lastDataDao = new LastDataDao();
        $companiesLicDao = new CompaniesLicenseDao();

        $dataCompany = $request->getParsedBody();
        /* Agregar datos a companies */
        $company = $companiesDao->addCompany($dataCompany);

        $lastId = $lastDataDao->findLastCompany();
        if (sizeof($_FILES) > 0) {
            $FilesDao->logoCompany($lastId['idCompany']);
        }
        /* Agregar datos a companies licenses */
        $company = $companiesLicDao->addLicense($dataCompany, $lastId['idCompany'], 2);

        if ($company == null)
            $resp = array('success' => true, 'message' => 'Datos de Empresa agregados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al ingresar la informacion. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    //Actualizar Empresa
    $group->post('/updateDataCompany', function (Request $request, Response $response, $args) {

        $companiesDao = new CompaniesDao();
        $FilesDao = new FilesDao();

        $dataCompany = $request->getParsedBody();
        $company = $companiesDao->updateCompany($dataCompany);

        if (sizeof($_FILES) > 0)
            $FilesDao->logoCompany($dataCompany['idCompany']);

        if ($company == null) {
            $resp = array('success' => true, 'message' => 'Datos de Empresa actualizados correctamente');
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error al actualizar la licencia. Intente nuevamente');
        }

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
