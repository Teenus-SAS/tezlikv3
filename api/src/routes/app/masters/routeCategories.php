<?php

use tezlikv3\dao\CategoriesDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\GeneralCategoriesDao;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

// Agrupar todas las rutas de categories bajo el prefijo '/categories'
$app->group('/categories', function (RouteCollectorProxy $group) {

    /* Consulta todos */
    $group->get('', function (Request $request, Response $response, $args) {
        $categoryDao = new CategoriesDao();

        // session_start();
        $id_company = $_SESSION['id_company'];
        $categories = $categoryDao->findAllCategoryByCompany($id_company);
        $response->getBody()->write(json_encode($categories, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/addCategory', function (Request $request, Response $response, $args) {
        $categoryDao = new CategoriesDao();
        $generalCategoryDao = new GeneralCategoriesDao();
        $lastDataDao = new LastDataDao();

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
                    // $category[$i]['idCategory'] = $findCategory['id_category'];
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

    $group->post('/updateCategory', function (Request $request, Response $response, $args) {
        $categoryDao = new CategoriesDao();
        $generalCategoryDao = new GeneralCategoriesDao();

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

    $group->get('/deleteCategory/{id_category}', function (Request $request, Response $response, $args) {
        $categoryDao = new CategoriesDao();

        $category = $categoryDao->deleteCategory($args['id_category']);

        if ($category == null)
            $resp = array('success' => true, 'message' => 'Categoria eliminada correctamente');

        if ($category != null)
            $resp = array('error' => true, 'message' => 'No es posible eliminar la categoria, existe información asociada a él');

        $response->getBody()->write(json_encode($resp));
        return $response->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
