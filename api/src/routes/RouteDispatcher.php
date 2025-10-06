<?php

declare(strict_types=1);

use Slim\App;

class RouteDispatcher
{
    private static $routeMap = [
        //Admin
        '/benefits' => '/admin/benefits/routeBenefits.php',
        '/binnacle' => '/admin/binnacle/routeBinnacle.php',
        '/binnacle' => '/admin/contract/routeContract.php',
        '/customers' => '/admin/customers/routeCustomers.php',
        '/customersUsers' => '/admin/customers/routeCustomersUsers.php',
        '/licenses' => '/admin/customers/routeLicense.php',
        '/usersAllowedByCompany' => '/admin/customers/routeUsersAllowed.php',
        '/panelAdmin' => '/admin/dashboard/routeDashboardGeneral.php',
        '/lastLogins' => '/admin/login/routeLastLoginsUsers.php',
        '/magnitudes' => '/admin/measurements/routeMagnitude.php',
        '/units' => '/admin/measurements/routeUnits.php',
        '/notifications' => '/admin/notifications/routeNotifications.php',
        '/plansAccess' => '/admin/plans/routePlanAccess.php',
        '/plans' => '/admin/plans/routePlans.php',
        '/quantityProductsGeneral'  => '/admin/products/routeQuantityProducts.php',
        '/puc'                      => '/admin/puc/routePucs.php',
        '/updateRisk'               => '/admin/risk/routeRisk.php',
        '/historicalTrm'            => '/admin/trm/routeTrm.php',
        '/lastLoginUsers'           => '/admin/users/routeActiveUsers.php',
        '/closeSessionUser'         => '/admin/users/routeCloseSessionUsers.php',
        '/userAdmins'               => '/admin/users/routeUserAdmin.php',

        // Login
        '/login'                    => '/login/routeLogin.php',
        '/logoutByInactivity'       => '/login/routeLogoutInactiveUser.php',
        '/ping'                     => '/login/routePing.php',

        // Notifications Users
        '/recentNotification'       => '/admin/notifications/routeShowNotifications.php',
        '/updatesNotice'            => '/global/routeUpdatesNotices.php',

        // Dashboard App
        '/panelGeneral'             => '/app/dashboard/general/routePanelGeneral.php',
        '/panelProducts'            => '/app/dashboard/products/routePanelProducts.php',

        '/multiproducts'            => '/app/tools/routeMultiproducts.php',

        // Prices
        '/prices'                   => '/app/prices/routePrices.php',
        '/customPrices'             => '/app/prices/customPrices/routeCustomPrices.php',
        '/priceList'                => '/app/general/routePriceList.php',

        // Utils
        '/measurements' => '/app/utils/routeMeasurements.php',
        '/productsMaterialsBasic' => '/app/utils/routeProductsMaterialsBasic.php',
        '/selectProducts' => '/app/utils/routeSelectProducts.php',
        '/selectMachines' => '/app/utils/routeSelectMachines.php',
        '/selectMaterials' => '/app/utils/routeSelectMaterials.php',
        '/benefitsPayroll' => '/app/utils/routeBenefitsPayroll.php',
        '/riskPayroll' => '/app/utils/routeRiskPayroll.php',
        '/rawMaterials' => '/app/utils/routeMaterialsBatch.php',
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
        '/factoryLoad' => '/app/config/routeFactoryLoad.php',
        '/generalServices' => '/app/config/routeGExternalServices.php',

        // General
        '/payroll' => '/app/general/routePayroll.php',
        '/expenses' => '/app/general/routeExpenses.php',
        '/recoveringExpenses' => '/app/general/routeExpenseRecover.php',
        '/expensesAnual' => '/app/general/routeExpensesAnual.php',

        '/distribution' => '/app/general/expensesDistribution/routeExpensesDistribution.php',
        '/annualDistribution' => '/app/general/expensesDistribution/routeExpensesDistributionAnual.php',
        '/distributionByFamilies' => '/app/general/expensesDistribution/routeFamilies.php',

        // Users
        '/users' => '/users/routeUsers.php',
        '/generalUser' => '/users/routeGeneralUserAccess.php',
        '/quantityUsers' => '/users/routeQuantityUsers.php',
        '/accessUsers' => '/users/routeUserAccess.php',

        // Tools
        '/negotiations' => '/app/tools/economyScale/routeEfficientNegotiations.php',
        '/objetivesPrices' => '/app/tools/economyScale/routePriceObjectives.php',
        '/saleObjectives' => '/app/tools/economyScale/routeSaleObjectives.php',

        '/rawMaterialsLots' => '/app/tools/analysisMaterials/routeLots.php',

        // Historic
        '/historical' => '/app/tools/historical/routeHistorical.php',
        '/dataHistorical' => '/app/tools/historical/routeDataHistorical.php',

        // Reports
        '/reports' => '/app/report/routeReport.php',

        // Quotations 
        '/companies' => '/app/quotes/routeCompanies.php',
        '/contacts' => '/app/quotes/routeContact.php',
        '/paymentMethods' => '/app/quotes/routePaymentMethods.php',
        '/quotes' => '/app/quotes/routeQuotes.php',

        // Profile
        '/company' => '/global/routeCompany.php',
        '/updateProfile' => '/global/routeProfile.php',
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
