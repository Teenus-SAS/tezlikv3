<?php

use tezlikv3\dao\CompanyUsers;

$companyUsers = new CompanyUsers();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/customersUsers', function (RouteCollectorProxy $group) use ($companyUsers) {

    $group->get('/{idCompany}', function (Request $request, Response $response, $args) use ($companyUsers) {
        $resp = $companyUsers->findCompanyUsers($args['idCompany']);
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    //Actualizar estado de usuarios * empresa
    $group->post('/{id_user}', function (Request $request, Response $response, $args) use ($companyUsers) {
        $sts = $companyUsers->userStatus($args['id_user']);
        $status = $sts['active'];

        if ($status == 1) {
            $licStatus = $companyUsers->updateCompanyUsersStatus(0, $args['id_user']);

            if ($licStatus == null) {
                $resp = array('success' => true, 'message' => 'Inactivo');
            } else {
                $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');
            }
        }

        if ($status == 0) {
            $licStatus = $companyUsers->updateCompanyUsersStatus(1, $args['id_user']);

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
