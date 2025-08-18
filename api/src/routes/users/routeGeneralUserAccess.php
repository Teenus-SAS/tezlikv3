<?php

use tezlikv3\dao\{
    CostUserAccessDao,
    GeneralUserAccessDao,
    PlanningUserAccessDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/generalUser', function (RouteCollectorProxy $group) {

    $group->get('/generalUserAccess/{id_user}', function (Request $request, Response $response, $args) {

        $userAccessDao = new GeneralUserAccessDao();

        $usersAcces = $userAccessDao->findUserAccessByUser($args['id_user']);
        $response->getBody()->write(json_encode($usersAcces, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/updateUserAccess', function (Request $request, Response $response, $args) {

        $costAccessUserDao = new CostUserAccessDao();
        $planningAccessUserDao = new PlanningUserAccessDao();
        $generalUAccessDao = new GeneralUserAccessDao();

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
})->add(new SessionMiddleware());
