<?php

use tezlikv3\dao\{
    FilesDao,
    LastDataDao,
    QCompaniesDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/companies', function (RouteCollectorProxy $group) {

    $group->get('/quotesCompanies', function (Request $request, Response $response, $args) {

        $companiesDao = new QCompaniesDao();

        $id_company = $_SESSION['id_company'];

        $companies = $companiesDao->findAllCompanies($id_company);
        $response->getBody()->write(json_encode($companies, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/addQCompany', function (Request $request, Response $response, $args) {

        $companiesDao = new QCompaniesDao();
        $lastDataDao = new LastDataDao();
        $FilesDao = new FilesDao();

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

    $group->post('/updateQCompany', function (Request $request, Response $response, $args) {

        $companiesDao = new QCompaniesDao();
        $FilesDao = new FilesDao();

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

    $group->get('/deleteQCompany/{id_company}', function (Request $request, Response $response, $args) {

        $companiesDao = new QCompaniesDao();

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
})->add(new SessionMiddleware());
