<?php

use tezlikv3\dao\{
    CompaniesLicenseDao,
    CompanyDao,
    GeneralCompanyLicenseDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/company', function (RouteCollectorProxy $group) {

    $group->get('', function (Request $request, Response $response, $args) {

        $companyDao = new CompanyDao();

        $id_company = $_SESSION['id_company'];
        $company = $companyDao->findDataCompanyByCompany($id_company);
        $response->getBody()->write(json_encode($company, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/changeDateContract/{op}', function (Request $request, Response $response, $args) {

        $companiesLicenseDao = new CompaniesLicenseDao();
        $generalCompanyLicenseDao = new GeneralCompanyLicenseDao();

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
})->add(new SessionMiddleware());
