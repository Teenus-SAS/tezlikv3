<?php

use tezlikv3\dao\CompaniesAllowedUsersDao;
use tezlikv3\dao\WebTokenDao;

$companiesAllowedUsersDao = new CompaniesAllowedUsersDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//USUARIOS PERMITIDOS POR EMPRESA

$app->get('/usersAllowedByCompany', function (Request $request, Response $response, $args) use (
    $companiesAllowedUsersDao,
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

    //EMPRESA Y USUARIOS PERMITIDOS
    $resp = $companiesAllowedUsersDao->usersAllowed();

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

//USUARIOS PERMITIDOS POR EMPRESA ACTUALIZACIÓN
//AGREGAR ID

$app->post('/updateUsersAllowedByCompany/{id_company}', function (Request $request, Response $response, $args) use (
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

    $dataUsers = $request->getParsedBody();

    //ACTUALIZAR NUMERO DE USUARIOS PERMITIDOS
    //$activeUsers = $companiesAllowedUsersDao->updateUsersAllowed(id_company);

    $resp = 0;

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
