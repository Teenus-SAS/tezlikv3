<?php

use tezlikv3\dao\{
    GenerateCodeDao,
    SendMakeEmailDao,
    SendEmailDao,
    UserAdminDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/userAdmins', function (RouteCollectorProxy $group) {

    $group->get('', function (Request $request, Response $response, $args) {

        $userAdminDao = new UserAdminDao();

        $userAdmin = $userAdminDao->findAllUserAdmins();
        $response->getBody()->write(json_encode($userAdmin, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/usersCompany', function (Request $request, Response $response, $args) {

        $userAdminDao = new UserAdminDao();

        $userAdmin = $userAdminDao->findAllUser();
        $response->getBody()->write(json_encode($userAdmin, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/userAdmin', function (Request $request, Response $response, $args) {

        $userAdminDao = new UserAdminDao();

        $userAdmin = $userAdminDao->findUserAdmin();
        $response->getBody()->write(json_encode($userAdmin, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/add', function (Request $request, Response $response, $args) {

        $userAdminDao = new UserAdminDao();
        $newPassDao = new GenerateCodeDao();
        $makeEmailDao = new SendMakeEmailDao();
        $emailDao = new SendEmailDao();

        // session_start();
        $email = $_SESSION['name'];
        $name = $_SESSION['email'];
        $dataUserAdmin = $request->getParsedBody();

        if (empty($dataUserAdmin['firstname']) || empty($dataUserAdmin['lastname']) || empty($dataUserAdmin['email']))
            $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
        else {
            $newPass = $newPassDao->GenerateCode();

            // Se envia email con usuario(email) y contraseña
            $dataEmail =  $makeEmailDao->SendEmailPassword($dataUserAdmin['email'], $newPass);

            $email = $emailDao->sendEmail($dataEmail, $email, $name);

            // if ($email == null)
            $userAdmin = $userAdminDao->insertUserAdmin($dataUserAdmin, $newPass);

            if ($userAdmin == null)
                $resp = array('success' => true, 'message' => 'Usuario creado correctamente');
            else if (isset($userAdmin['info']))
                $resp = array('info' => true, 'message' => $userAdmin['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        }
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/changeCompany', function (Request $request, Response $response, $args) {

        $userAdminDao = new UserAdminDao();

        $dataUserAdmin = $request->getParsedBody();

        $user = $userAdminDao->changeCompanyUser($dataUserAdmin);

        if ($user == null)
            $resp = array('success' => true, 'message' => 'Compañia modificada correctamente');
        else if (isset($user['info']))
            $resp = array('info' => true, 'message' => $user['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al modificar la información. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/update', function (Request $request, Response $response, $args) {

        $userAdminDao = new UserAdminDao();

        $email = $_SESSION['email'];
        $dataUserAdmin = $request->getParsedBody();

        if (empty($dataUserAdmin['firstname']) || empty($dataUserAdmin['lastname']) || empty($dataUserAdmin['email']))
            $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
        else {

            if ($dataUserAdmin['email'] == $email) {
                $resp = array('error' => true, 'message' => 'No puede actualizar este usuario');
                return false;
            }

            $userAdmin = $userAdminDao->updateUser($dataUserAdmin);

            if ($userAdmin == null)
                $resp = array('success' => true, 'message' => 'Usuario modificado correctamente');
            else if (isset($userAdmin['info']))
                $resp = array('info' => true, 'message' => $userAdmin['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
        }
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/delete', function (Request $request, Response $response, $args) {

        $userAdminDao = new UserAdminDao();

        $email = $_SESSION['email'];
        $dataUserAdmin = $request->getParsedBody();

        if ($dataUserAdmin['email'] == $email) {
            $resp = array('error' => true, 'message' => 'No puede eliminar este usuario');
            return false;
        }

        $userAdmin = $userAdminDao->deleteUser($dataUserAdmin['idAdmin']);

        if ($userAdmin == null)
            $resp = array('success' => true, 'message' => 'Usuario eliminado correctamente');
        else if (isset($userAdmin['info']))
            $resp = array('info' => true, 'message' => $userAdmin['info']);
        else
            $resp = array('error' => true, 'message' => 'No es posible eliminar el usuario, existe información asociada a él');

        $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
