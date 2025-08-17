<?php

use tezlikv3\dao\UpdatesNoticeDao;

$updatesNoticesDao = new UpdatesNoticeDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Verificar codigo */

$app->get('/updatesNotice', function (Request $request, Response $response, $args) use ($updatesNoticesDao) {
    $id_user = $_SESSION['idUser'];
    $updatesNoticesDao->updatesNoticeDao($id_user);
    return ResponseHelper::withJson($response, ['message' => 'Nos vemos pronto'], 200);
})->add(new SessionMiddleware());
