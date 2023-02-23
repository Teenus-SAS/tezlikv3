<?php

use tezlikv3\dao\CostUserAccessDao;
use tezlikv3\dao\GeneralUserAccessDao;
use tezlikv3\dao\PlanningUserAccessDao;

$costAccessUserDao = new CostUserAccessDao();
$planningAccessUserDao = new PlanningUserAccessDao();
$userAccessDao = new GeneralUserAccessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/generalUserAccess/{id_user}', function (Request $request, Response $response, $args) use ($userAccessDao) {
    $usersAcces = $userAccessDao->findUserAccessByUser($args['id_user']);
    $response->getBody()->write(json_encode($usersAcces, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updateUserAccess', function (Request $request, Response $response, $args) use (
    $costAccessUserDao,
    $planningAccessUserDao,
    $generalUAccessDao
) {
    $dataUserAccess = $request->getParsedBody();

    /* Almacena los accesos */
    $usersAccess = $costAccessUserDao->updateUserAccessByUsers($dataUserAccess);
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
