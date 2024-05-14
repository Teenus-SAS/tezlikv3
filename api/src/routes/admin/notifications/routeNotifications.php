<?php

use tezlikv3\dao\NotificationsDao;
use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\SendMakeEmailDao;
use tezlikv3\dao\UsersDao;
use tezlikv3\dao\WebTokenDao;

$notificationsDao = new NotificationsDao();
$sendMakeEmailDao = new SendMakeEmailDao();
$webTokenDao = new WebTokenDao();
$sendEmailDao = new SendEmailDao();
$usersDao = new UsersDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/notifications', function (Request $request, Response $response, $args) use (
    $notificationsDao,
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

    $notifications = $notificationsDao->findAllNotifications();
    $response->getBody()->write(json_encode($notifications));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/recentNotification', function (Request $request, Response $response, $args) use (
    $notificationsDao,
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

    // session_start();
    $id_company = $_SESSION['id_company'];

    !$id_company ? $id_company = '' : $id_company;

    $notifications = $notificationsDao->findRecentNotification($id_company);
    $response->getBody()->write(json_encode($notifications));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/addNotification', function (Request $request, Response $response, $args) use (
    $notificationsDao,
    $webTokenDao,
    $usersDao,
    $sendMakeEmailDao,
    $sendEmailDao
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

$app->post('/updateNotification', function (Request $request, Response $response, $args) use (
    $notificationsDao,
    $webTokenDao,
    $usersDao,
    $sendMakeEmailDao,
    $sendEmailDao
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

$app->get('/updateCheckNotification', function (Request $request, Response $response, $args) use (
    $notificationsDao,
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

    // session_start();
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

$app->get('/deleteNotification/{id_notification}', function (Request $request, Response $response, $args) use (
    $notificationsDao,
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

    $notifications = $notificationsDao->deleteNotification($args['id_notification']);

    if ($notifications == null)
        $resp = array('success' => true, 'message' => 'Notificacion eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la notificación, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
