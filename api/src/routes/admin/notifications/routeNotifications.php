<?php

use tezlikv3\dao\{
    NotificationsDao,
    SendEmailDao,
    SendMakeEmailDao,
    UsersDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/notifications', function (RouteCollectorProxy $group) {

    $group->get('', function (Request $request, Response $response, $args) {

        $notificationsDao = new NotificationsDao();

        $notifications = $notificationsDao->findAllNotifications();
        $response->getBody()->write(json_encode($notifications));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/add', function (Request $request, Response $response, $args) {

        $notificationsDao = new NotificationsDao();
        $sendMakeEmailDao = new SendMakeEmailDao();
        $sendEmailDao = new SendEmailDao();
        $usersDao = new UsersDao();

        $dataNotifications = $request->getParsedBody();

        $resolution = $notificationsDao->insertNotification($dataNotifications);

        $users = $usersDao->findAllUsersByCompany($dataNotifications['company']);

        for ($i = 0; $i < sizeof($users); $i++) {
            if (isset($resolution['info'])) break;

            $name  = $users[$i]['firstname'] . " " . $users[$i]['lastname'];

            $dataEmail = $sendMakeEmailDao->SendEmailNotifications($name, $users[$i]['email'], $dataNotifications['description']);
            $resolution = $sendEmailDao->sendEmail($dataEmail, $users[$i]['email'], $name);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Notificacion ingresada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/update', function (Request $request, Response $response, $args) {
        $notificationsDao = new NotificationsDao();
        $sendMakeEmailDao = new SendMakeEmailDao();
        $sendEmailDao = new SendEmailDao();
        $usersDao = new UsersDao();

        $dataNotifications = $request->getParsedBody();

        if (empty($dataNotifications['description']) || empty($dataNotifications['company']))
            $resp = array('error' => true, 'message' => 'No hubo algun cambio');
        else {
            $notifications = $notificationsDao->updateNotification($dataNotifications);
            $users = $usersDao->findAllUsersByCompany($dataNotifications['company']);

            for ($i = 0; $i < sizeof($users); $i++) {
                if (isset($resolution['info'])) break;

                $name  = $users[$i]['firstname'] . " " . $users[$i]['lastname'];

                $dataEmail = $sendMakeEmailDao->SendEmailNotifications($name, $users[$i]['email'], $dataNotifications['description']);
                $resolution = $sendEmailDao->sendEmail($dataEmail, $users[$i]['email'], $name);
            }

            if ($notifications == null)
                $resp = array('success' => true, 'message' => 'Notificacion modificada correctamente');
            else if (isset($notifications['info']))
                $resp = array('info' => true, 'message' => $notifications['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
        }
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/check', function (Request $request, Response $response, $args) {

        $notificationsDao = new NotificationsDao();

        $id_company = $_SESSION['id_company'];

        $notifications = $notificationsDao->updateCheckNotification($id_company);

        if ($notifications == null)
            $resp = array('success' => true, 'message' => 'Notificaciones limpiadas correctamente');
        else if (isset($notifications['info']))
            $resp = array('info' => true, 'message' => $notifications['message']);
        else
            $resp = array('success' => true, 'message' => 'No se pudo limpiar las notificaciones. Intente nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/delete/{id_notification}', function (Request $request, Response $response, $args) {

        $notificationsDao = new NotificationsDao();

        $notifications = $notificationsDao->deleteNotification($args['id_notification']);

        if ($notifications == null)
            $resp = array('success' => true, 'message' => 'Notificacion eliminada correctamente');
        else
            $resp = array('error' => true, 'message' => 'No es posible eliminar la notificación, existe información asociada a ella');

        $response->getBody()->write(json_encode($resp));
        return $response->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
