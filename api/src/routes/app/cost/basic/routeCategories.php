<?php

use tezlikv3\dao\CategoriesDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\GeneralCategoriesDao;
use tezlikv3\dao\WebTokenDao;

$categoryDao = new CategoriesDao();
$webTokenDao = new WebTokenDao();
$generalCategoryDao = new GeneralCategoriesDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/categories', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $categoryDao
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
    $categories = $categoryDao->findAllCategoryByCompany($id_company);
    $response->getBody()->write(json_encode($categories, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/categoriesDataValidation', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalCategoryDao
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

    $dataCategory = $request->getParsedBody();

    if (isset($dataCategory)) {
        // session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $category = $dataCategory['importCategories'];

        for ($i = 0; $i < sizeof($category); $i++) {
            if (empty($category[$i]['category'])) {
                $i = $i + 2;
                $dataImportCategory = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }
            if (empty(trim($category[$i]['category']))) {
                $i = $i + 2;
                $dataImportCategory = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            } else {
                $findCategory = $generalCategoryDao->findCategory($category[$i], $id_company);
                if (!$findCategory) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportCategory['insert'] = $insert;
                $dataImportCategory['update'] = $update;
            }
        }
    } else
        $dataImportCategory = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportCategory, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addCategory', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $categoryDao,
    $generalCategoryDao,
    $lastDataDao
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
    $dataCategory = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    if (empty($dataCategory['importCategories'])) {

        $category = $generalCategoryDao->findCategory($dataCategory, $id_company);

        if (!$category) {
            $category = $categoryDao->insertCategoryByCompany($dataCategory, $id_company);

            if ($category == null)
                $resp = array('success' => true, 'message' => 'Categoria creada correctamente');
            else if (isset($category['info']))
                $resp = array('info' => true, 'message' => $category['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Categoria duplicado. Ingrese una nuevo Categoria');
    } else {
        $category = $dataCategory['importCategories'];

        for ($i = 0; $i < sizeof($category); $i++) {
            if (isset($resolution['info'])) break;

            $findCategory = $generalCategoryDao->findCategory($category[$i], $id_company);
            if (!$findCategory) {
                $resolution = $categoryDao->insertCategoryByCompany($category[$i], $id_company);
            } else {
                $category[$i]['idCategory'] = $findCategory['id_category'];
                $resolution = $categoryDao->updateCategory($category[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Categoria importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateCategory', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $categoryDao,
    $generalCategoryDao
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
    $dataCategory = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $data = [];

    $category = $generalCategoryDao->findCategory($dataCategory, $id_company);

    !is_array($category) ? $data['id_category'] = 0 : $data = $category;

    if ($data['id_category'] == $dataCategory['idCategory'] || $data['id_category'] == 0) {
        $category = $categoryDao->updateCategory($dataCategory);

        if ($category == null)
            $resp = array('success' => true, 'message' => 'Categoria actualizada correctamente');
        else if (isset($category['info']))
            $resp = array('info' => true, 'message' => $category['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Categoria duplicada. Ingrese una nueva categoria');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteCategory/{id_category}', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $categoryDao
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

    $category = $categoryDao->deleteCategory($args['id_category']);

    if ($category == null)
        $resp = array('success' => true, 'message' => 'Categoria eliminada correctamente');

    if ($category != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar la categoria, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
