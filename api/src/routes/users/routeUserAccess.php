<?php

use tezlikv3\dao\{
    CompaniesLicenseDao,
    CostUserAccessDao,
    GeneralUserAccessDao,
    LastDataDao,
    UsersDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/accessUsers', function (RouteCollectorProxy $group) {

    $group->get('/costUsersAccess', function (Request $request, Response $response, $args) {

        $userAccessDao = new CostUserAccessDao();

        $company = isset($_SESSION['id_company']) ? $_SESSION['id_company'] : 0;
        $usersAccess = $userAccessDao->findAllUsersAccess($company);
        $response->getBody()->write(json_encode($usersAccess, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/costUserAccess', function (Request $request, Response $response, $args) {

        $userAccessDao = new CostUserAccessDao();

        $company = isset($_SESSION['id_company']) ? $_SESSION['id_company'] : 0;
        $id_user = $_SESSION['idUser'];
        $userAccess = $userAccessDao->findUserAccess($company, $id_user);
        // $userAccess = $userAccess[0];
        $response->getBody()->write(json_encode($userAccess, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /* $group->post('/addCostUserAccess', function (Request $request, Response $response, $args) {

        $lastDataDao = new LastDataDao();
        $userAccessDao = new CostUserAccessDao();
        $generalUAccessDao = new GeneralUserAccessDao();

        $dataUserAccess = $request->getParsedBody();
        $id_company = $_SESSION['id_company'];

        if (
            empty($dataUserAccess['createProduct']) && empty($dataUserAccess['costCreateMaterials']) &&
            empty($dataUserAccess['costCreateMachines']) && empty($dataUserAccess['costCreateProcess'])
        )
            $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
        else {

            if (isset($dataUserAccess['id_user']))
                $user = $dataUserAccess;
            else {
                $user = $lastDataDao->findLastInsertedUser($id_company);
            }

            $userAccess = $userAccessDao->insertUserAccessByUser($dataUserAccess, $id_company);

            // Modificar accesos 
            $generalUAccessDao->setGeneralAccess($user['idUser']);

            if ($userAccess == null)
                $resp = array('success' => true, 'message' => 'Acceso de usuario creado correctamente');
            else if (isset($userAccess['info']))
                $resp = array('info' => true, 'message' => $userAccess['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
        }
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }); */

    $group->post('/updateCostUserAccess', function (Request $request, Response $response, $args) {

        $userAccessDao = new CostUserAccessDao();
        $generalUAccessDao = new GeneralUserAccessDao();

        $id_company = $_SESSION['id_company'];
        $idUser = $_SESSION['idUser'];

        $dataUserAccess = $request->getParsedBody();

        $findUserAccess = $userAccessDao->findUserAccess($id_company, $dataUserAccess['id_user']);

        if (sizeof($dataUserAccess['typeCustomPrices']) == 1)
            $typeCustomPrice = $dataUserAccess['typeCustomPrices'][0];
        else
            $typeCustomPrice = implode(',', $dataUserAccess['typeCustomPrices']);

        if ($findUserAccess)
            $userAccess = $userAccessDao->updateUserAccessByUsers($dataUserAccess, $typeCustomPrice);
        else
            $userAccess = $userAccessDao->insertUserAccessByUser($dataUserAccess, $typeCustomPrice);

        // if ($dataUserAccess['typeExpenses'] != 0) {
        //     $companiesLicenseDao->changeFlagExpense($dataUserAccess, $id_company);
        // }

        /* Modificar accesos */
        if ($idUser == $dataUserAccess['id_user'])
            $generalUAccessDao->setGeneralAccess($dataUserAccess['id_user']);

        if ($userAccess == null)
            $resp = array('success' => true, 'message' => 'Acceso de usuario actualizado correctamente');
        elseif ($userAccess == 1)
            $resp = array('error' => true, 'message' => 'No puede actualizar este usuario');
        else if (isset($userAccess['info']))
            $resp = array('info' => true, 'message' => $userAccess['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
