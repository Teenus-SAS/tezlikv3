<?php

declare(strict_types=1);

use Slim\App;

class RouteDispatcher
{
    private static $routeMap = [
        '/login' => '/login/routeLogin.php',
        '/recentNotification' => '/admin/notifications/routeShowNotifications.php',

        '/panelGeneral' => '/app/dashboard/general/routePanelGeneral.php',
        '/panelProducts' => '/app/dashboard/products/routePanelProducts.php',

        '/multiproducts' => '/app/tools/routeMultiproducts.php',

        '/prices' => '/app/prices/routePrices.php',
        '/customPrices' => '/app/prices/customPrices/routeCustomPrices.php',
        '/priceList' => '/app/general/routePriceList.php',

        // Utils
        '/measurements' => '/app/utils/routeMeasurements.php',
        '/productsMaterialsBasic' => '/app/utils/routeProductsMaterialsBasic.php',
        '/selectProducts' => '/app/utils/routeSelectProducts.php',
        '/selectMachines' => '/app/utils/routeSelectMachines.php',
        '/selectMaterials' => '/app/utils/routeSelectMaterials.php',
        '/calculations' => '/app/calculations/routeCalc.php',

        // Masters
        '/categories' => '/app/masters/routeCategories.php',
        '/machines' => '/app/masters/routeMachines.php',
        '/materials' => '/app/masters/routeMaterials.php',
        '/process' => '/app/masters/routeProcess.php',
        '/products' => '/app/masters/routeProducts.php',

        // Config
        '/dataSheetMaterials' => '/app/config/routeProductsMaterials.php',
        '/dataSheetProcess' => '/app/config/routeProductsProcess.php',
        '/dataSheetServices' => '/app/config/routeExternalServices.php',
        '/subproducts' => '/app/config/routeCompositesProducts.php',




    ];

    public static function dispatch(App $app, string $path): void
    {
        // Extraer el segmento principal del path
        $basePath = self::extractBasePath($path);

        if ($basePath && isset(self::$routeMap[$basePath])) {
            $routeFile = __DIR__ . self::$routeMap[$basePath];

            if (file_exists($routeFile)) {
                require_once $routeFile;
                return;
            }
        }

        // Respuesta rápida cuando no se encuentra la ruta
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
        exit;
    }

    private static function extractBasePath(string $path): ?string
    {
        // Eliminar parámetros de query si existen
        $path = strtok($path, '?');

        // Dividir el path por /
        $parts = explode('/', trim($path, '/'));

        // El primer segmento no vacío es el basePath
        return isset($parts[0]) ? '/' . $parts[0] : null;
    }
}
