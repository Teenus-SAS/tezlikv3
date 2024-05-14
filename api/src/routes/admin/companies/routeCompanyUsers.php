<?php

use tezlikv3\dao\CompanyUsers;
use tezlikv3\dao\WebTokenDao;

$companyUsers = new CompanyUsers();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Obtener usuarios * empresa
$app->get('/companyUsers/{idCompany}', function (Request $request, Response $response, $args) use (
    $companyUsers,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $resp = $companyUsers->findCompanyUsers($args['idCompany']);
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


//Actualizar estado de usuarios * empresa
$app->post('/updateCompanyUsersStatus/{id_user}', function (Request $request, Response $response, $args) use (
    $companyUsers,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

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
