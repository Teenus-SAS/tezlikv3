<?php

use tezlikv3\dao\CompaniesAllowedUsersDao;

$companiesAllowedUsersDao = new CompaniesAllowedUsersDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/usersAllowedByCompany', function (RouteCollectorProxy $group) use ($companiesAllowedUsersDao) {

    $group->get('', function (Request $request, Response $response, $args) use ($companiesAllowedUsersDao) {
        //EMPRESA Y USUARIOS PERMITIDOS
        $resp = $companiesAllowedUsersDao->usersAllowed();

        $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /*  $group->post('/updateUsersAllowedByCompany/{id_company}', function (Request $request, Response $response, $args) {
        $dataUsers = $request->getParsedBody();

        //ACTUALIZAR NUMERO DE USUARIOS PERMITIDOS
        //$activeUsers = $companiesAllowedUsersDao->updateUsersAllowed(id_company);

        $resp = 0;

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }); */
})->add(new SessionMiddleware());
