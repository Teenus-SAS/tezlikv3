<?php

use tezlikv3\dao\PlanAccessDao;

$plansAccessDao = new PlanAccessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/plansAccess', function (RouteCollectorProxy $group) use ($plansAccessDao) {

    $group->get('', function (Request $request, Response $response, $args) use ($plansAccessDao) {
        $plans = $plansAccessDao->findAllPlansAccess();

        $response->getBody()->write(json_encode($plans));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/plan', function (Request $request, Response $response, $args) use ($plansAccessDao) {
        $id_plan = $_SESSION['plan'];

        $plan = $plansAccessDao->findPlanAccess($id_plan);

        $response->getBody()->write(json_encode($plan));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/update', function (Request $request, Response $response, $args) use ($plansAccessDao) {
        $dataPlan = $request->getParsedBody();

        $plans = $plansAccessDao->updateAccessPlan($dataPlan);

        if ($plans == null)
            $resp = array('success' => true, 'message' => 'Se modificaron los accesos del plan correctamente');
        else if ($plans['info'] == true)
            $resp = array('info' => true, 'message' => $plans['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
