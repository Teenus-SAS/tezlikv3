<?php

use tezlikv3\dao\NotificationsDao;

$notificationsDao = new NotificationsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/recentNotification', function (Request $request, Response $response, $args) use ($notificationsDao) {
    $id_company = $_SESSION['id_company'];

    !$id_company ? $id_company = '' : $id_company;

    $notifications = $notificationsDao->findRecentNotification($id_company);
    $response->getBody()->write(json_encode($notifications));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
