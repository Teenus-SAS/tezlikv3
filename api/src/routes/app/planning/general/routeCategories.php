<?php

use tezlikv3\dao\CategoriesDao;

$categoriesDao = new CategoriesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/categories', function (Request $request, Response $response, $args) use ($categoriesDao) {
    $categories = $categoriesDao->findAllCategories();
    $response->getBody()->write(json_encode($categories, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/categoriesDataValidation', function (Request $request, Response $response, $args) use ($categoriesDao) {
    $dataCategories = $request->getParsedBody();

    if (isset($dataCategories)) {

        $insert = 0;
        $update = 0;

        $categories = $dataCategories['importCategories'];

        for ($i = 0; $i < sizeof($categories); $i++) {
            $nameCategory = $categories[$i]['category'];
            if (empty($nameCategory)) {
                $i = $i + 1;
                $dataimportCategories = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            } else {
                $findCategory = $categoriesDao->findCategory($categories[$i]);
                if (!$findCategory) $insert = $insert + 1;
                else $update = $update + 1;
                $dataimportCategories['insert'] = $insert;
                $dataimportCategories['update'] = $update;
            }
        }
    } else
        $dataimportCategories = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataimportCategories, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addCategory', function (Request $request, Response $response, $args) use ($categoriesDao) {
    $dataCategories = $request->getParsedBody();

    if (empty($dataCategories['importCategories'])) {
        $category = $categoriesDao->insertCategory($dataCategories);

        if ($category == null)
            $resp = array('success' => true, 'message' => 'Categoria creada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $categories = $dataCategories['importCategories'];

        for ($i = 0; $i < sizeof($categories); $i++) {
            $findCategory = $categoriesDao->findCategory($categories[$i]);
            if (!$findCategory)
                $resolution = $categoriesDao->insertCategory($categories[$i]);
            else {
                $categories[$i]['idCategory'] = $findCategory['id_category'];
                $resolution = $categoriesDao->updateCategory($categories[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Categoria importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateCategory', function (Request $request, Response $response, $args) use ($categoriesDao) {
    $dataCategories = $request->getParsedBody();

    if (empty($dataCategories['category']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    else {
        $category = $categoriesDao->updateCategory($dataCategories);

        if ($category == null)
            $resp = array('success' => true, 'message' => 'Categoria actualizada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteCategory/{id_category}', function (Request $request, Response $response, $args) use ($categoriesDao) {
    $category = $categoriesDao->deleteCategory($args['id_category']);

    if ($category == null)
        $resp = array('success' => true, 'message' => 'Categoria eliminada correctamente');

    if ($category != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar la Categoria, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
