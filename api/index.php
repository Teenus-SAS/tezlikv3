<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/AutoloaderSourceCode.php';

$app = AppFactory::create();
$app->setBasePath('/api');

// OPCIÓN 1: Lazy Loading de Rutas
// Solo carga las rutas cuando se necesiten
class RouteLoader
{
    private static $routes = [
        // Admin routes
        'admin/companies' => [
            '../api/src/routes/admin/companies/routeCompanies.php',
            '../api/src/routes/admin/companies/routeCompaniesLicense.php',
            '../api/src/routes/admin/companies/routeUsersAllowed.php',
            '../api/src/routes/admin/companies/routeCompanyUsers.php',
        ],
        'admin/plans' => [
            '../api/src/routes/admin/plans/routePlans.php',
            '../api/src/routes/admin/plans/routePlanAccess.php',
        ],
        'admin/login' => [
            '../api/src/routes/admin/login/routeLastLoginsUsers.php',
        ],
        'admin/products' => [
            '../api/src/routes/admin/products/routeQuantityProducts.php',
        ],
        'admin/puc' => [
            '../api/src/routes/admin/puc/routePucs.php',
        ],
        'admin/notifications' => [
            '../api/src/routes/admin/notifications/routeNotifications.php',
        ],
        'admin/users' => [
            '../api/src/routes/admin/users/routeActiveUsers.php',
            '../api/src/routes/admin/users/routeCloseSessionUsers.php',
            '../api/src/routes/admin/users/routeUserAdmin.php',
        ],
        'admin/benefits' => [
            '../api/src/routes/admin/benefits/routeBenefits.php',
        ],
        'admin/risks' => [
            '../api/src/routes/admin/risks/routeRisks.php',
        ],
        'admin/trm' => [
            '../api/src/routes/admin/trm/routeTrm.php',
        ],
        'admin/units' => [
            '../api/src/routes/admin/units/routeUnits.php',
        ],
        'admin/magnitude' => [
            '../api/src/routes/admin/magnitude/routeMagnitude.php',
        ],
        'admin/binnacle' => [
            '../api/src/routes/admin/binnacle/routeBinnacle.php',
        ],
        'admin/dashboard' => [
            '../api/src/routes/admin/dashboard/routeDashboardGeneral.php',
        ],
        'admin/contract' => [
            '../api/src/routes/admin/contract/routeContract.php',
        ],

        // App routes
        'app/cost/trm' => [
            '../api/src/routes/app/cost/trm/routeTrm.php',
        ],
        'app/cost/quotes' => [
            '../api/src/routes/app/cost/quotes/routeQuotes.php',
            '../api/src/routes/app/cost/quotes/routeCompanies.php',
            '../api/src/routes/app/cost/quotes/routeContacts.php',
            '../api/src/routes/app/cost/quotes/routePaymentMethods.php',
        ],
        'app/cost/basic' => [
            '../api/src/routes/app/cost/basic/routeProcess.php',
            '../api/src/routes/app/cost/basic/routeMachines.php',
            '../api/src/routes/app/cost/basic/routeMaterials.php',
            '../api/src/routes/app/cost/basic/routeCategories.php',
            '../api/src/routes/app/cost/basic/routeProducts.php',
        ],
        'app/cost/config' => [
            '../api/src/routes/app/cost/config/routeExternalServices.php',
            '../api/src/routes/app/cost/config/routeGExternalServices.php',
            '../api/src/routes/app/cost/config/routeFactoryLoad.php',
            '../api/src/routes/app/cost/config/routeProductsProcess.php',
            '../api/src/routes/app/cost/config/routeCompositesProducts.php',
            '../api/src/routes/app/cost/config/routeProductsMaterials.php',
        ],
        'app/cost/dashboard' => [
            '../api/src/routes/app/cost/dashboard/routeDashboardGenerals.php',
            '../api/src/routes/app/cost/dashboard/routeDashboardProducts.php',
        ],
        'app/cost/general' => [
            '../api/src/routes/app/cost/general/routeExpenses.php',
            '../api/src/routes/app/cost/general/expensesDistribution/routeExpensesDistribution.php',
            '../api/src/routes/app/cost/general/routeExpensesAnual.php',
            '../api/src/routes/app/cost/general/expensesDistribution/routeExpensesDistributionAnual.php',
            '../api/src/routes/app/cost/general/expensesDistribution/routeFamilies.php',
            '../api/src/routes/app/cost/general/routeExpenseRecover.php',
            '../api/src/routes/app/cost/general/routePayroll.php',
            '../api/src/routes/app/cost/general/routeProcessPayroll.php',
            '../api/src/routes/app/cost/general/routePuc.php',
            '../api/src/routes/app/cost/general/routePriceList.php',
            '../api/src/routes/app/cost/general/routeProductionCenter.php',
        ],
        'app/cost/prices' => [
            '../api/src/routes/app/cost/prices/routePrices.php',
            '../api/src/routes/app/cost/prices/routePricesUSD.php',
            '../api/src/routes/app/cost/prices/routePricesEUR.php',
            '../api/src/routes/app/cost/prices/customPrices/routesCustomPrices.php',
            '../api/src/routes/app/cost/prices/customPrices/routeCustomPercentage.php',
        ],
        'app/cost/support' => [
            '../api/src/routes/app/cost/support/routeSupport.php',
        ],
        'app/cost/tools' => [
            '../api/src/routes/app/cost/tools/routeMultiproducts.php',
            '../api/src/routes/app/cost/tools/analysisMaterials/routeProducts.php',
            '../api/src/routes/app/cost/tools/analysisMaterials/routeLots.php',
            '../api/src/routes/app/cost/tools/routeSimulator.php',
            '../api/src/routes/app/cost/tools/routeHistorical.php',
            '../api/src/routes/app/cost/tools/economyScale/routeEfficientNegotiations.php',
            '../api/src/routes/app/cost/tools/economyScale/routeSaleObjectives.php',
            '../api/src/routes/app/cost/tools/economyScale/routePriceObjectives.php',
        ],
        'app/cost/report' => [
            '../api/src/routes/app/cost/report/routeReport.php',
        ],
        'app/cost/userAccess' => [
            '../api/src/routes/app/cost/userAccess/routeUserAccess.php',
        ],
        'app/global' => [
            '../api/src/routes/app/global/routeCompany.php',
            '../api/src/routes/app/global/routeDoubleFactor.php',
            '../api/src/routes/app/global/routeUpdatesNotices.php',
            '../api/src/routes/app/global/routeProfile.php',
        ],
        'app/login' => [
            '../api/src/routes/app/login/routeLogin.php',
            '../api/src/routes/app/login/routepassUser.php',
            '../api/src/routes/app/login/routeInactiveUser.php',
        ],
        'app/users' => [
            '../api/src/routes/app/users/routeGeneralUserAccess.php',
            '../api/src/routes/app/users/routeUsers.php',
            '../api/src/routes/app/users/routeQuantityUsers.php',
        ],
    ];

    private static $loadedGroups = [];

    public static function loadRouteGroup($groupName)
    {
        if (!isset(self::$loadedGroups[$groupName]) && isset(self::$routes[$groupName])) {
            foreach (self::$routes[$groupName] as $routeFile) {
                if (file_exists($routeFile)) {
                    require_once $routeFile;
                }
            }
            self::$loadedGroups[$groupName] = true;
        }
    }

    public static function loadRoutesForPath($path)
    {
        foreach (self::$routes as $groupName => $files) {
            if (strpos($path, $groupName) === 0) {
                self::loadRouteGroup($groupName);
                break;
            }
        }
    }
}

// Middleware para cargar rutas bajo demanda
$app->add(function ($request, $handler) {
    $path = $request->getUri()->getPath();
    // Remover el base path
    $path = str_replace('/api/', '', $path);

    RouteLoader::loadRoutesForPath($path);

    return $handler->handle($request);
});

$app->run();
