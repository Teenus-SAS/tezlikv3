<?php

use tezlikv3\dao\CostUserAccessDao;
use tezlikv3\dao\GeneralUserAccessDao;
use tezlikv3\dao\PlanningUserAccessDao;
use tezlikv3\dao\WebTokenDao;

$costAccessUserDao = new CostUserAccessDao();
$webTokenDao = new WebTokenDao();
$planningAccessUserDao = new PlanningUserAccessDao();
$userAccessDao = new GeneralUserAccessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/generalUserAccess/{id_user}', function (Request $request, Response $response, $args) use (
    $userAccessDao,
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

    $usersAcces = $userAccessDao->findUserAccessByUser($args['id_user']);
    $response->getBody()->write(json_encode($usersAcces, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updateUserAccess', function (Request $request, Response $response, $args) use (
    $costAccessUserDao,
    $webTokenDao,
    $planningAccessUserDao,
    $generalUAccessDao
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

    $dataUserAccess = $request->getParsedBody();

    /* Almacena los accesos */
    $usersAccess = $costAccessUserDao->updateUserAccessByUsers($dataUserAccess, '');
    if ($usersAccess == null)
        $usersAccess = $planningAccessUserDao->updateUserAccessByUsers($dataUserAccess);

    /* Modificar accesos */
    if ($usersAccess == null)
        $generalUAccessDao->setGeneralAccess($dataUserAccess['idUser']);

    if ($usersAccess == null)
        $resp = array('success' => true, 'message' => 'Acceso de usuario actualizado correctamente');
    elseif ($usersAccess == 1)
        $resp = array('error' => true, 'message' => 'No puede actualizar este usuario');
    else if (isset($usersAccess['info']))
        $resp = array('info' => true, 'message' => $usersAccess['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
