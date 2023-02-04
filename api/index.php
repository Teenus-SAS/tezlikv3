<?php


use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/AutoloaderSourceCode.php';

$app = AppFactory::create();
$app->setBasePath('/api');

/* Admin */

// Companies
require_once('../api/src/routes/admin/companies/routeCompanies.php');
require_once('../api/src/routes/admin/companies/routeCompaniesLicense.php');
require_once('../api/src/routes/admin/companies/routeUsersAllowed.php');
require_once('../api/src/routes/admin/companies/routeCompanyUsers.php');

// Plan
require_once('../api/src/routes/admin/plans/routePlans.php');

// Login
require_once('../api/src/routes/admin/login/routeLastLoginsUsers.php');

// Products
require_once('../api/src/routes/admin/products/routeQuantityProducts.php');


// Notifications
require_once('../api/src/routes/admin/notifications/routeNotifications.php');
// Users
require_once('../api/src/routes/admin/users/routeActiveUsers.php');
require_once('../api/src/routes/admin/users/routeCloseSessionUsers.php');
require_once('../api/src/routes/admin/users/routeUserAdmin.php');
require_once('../api/src/routes/app/login/routeInactiveUser.php');

// Dashboard
require_once('../api/src/routes/admin/dashboard/routeDashboardGeneral.php');

/* App Cost */
// Quotes
require_once('../api/src/routes/app/cost/quotes/routeQuotes.php');
require_once('../api/src/routes/app/cost/quotes/routeCompanies.php');
require_once('../api/src/routes/app/cost/quotes/routeContacts.php');
require_once('../api/src/routes/app/cost/quotes/routePaymentMethods.php');

// Analysis
require_once('../api/src/routes/app/cost/analysis/routeReviewRawMaterials.php');

// Basic
require_once('../api/src/routes/app/cost/basic/routeProcess.php');
require_once('../api/src/routes/app/cost/basic/routeMachines.php');
require_once('../api/src/routes/app/cost/basic/routeMaterials.php');
require_once('../api/src/routes/app/cost/basic/routeProducts.php');

// Config
require_once('../api/src/routes/app/cost/config/routeExternalServices.php');
require_once('../api/src/routes/app/cost/config/routeFactoryLoad.php');
require_once('../api/src/routes/app/cost/config/routeProductsProcess.php');
require_once('../api/src/routes/app/cost/config/routeProductsMaterials.php');

// Dashboard
require_once('../api/src/routes/app/cost/dashboard/routeDashboardGenerals.php');
require_once('../api/src/routes/app/cost/dashboard/routeDashboardProducts.php');


// General
require_once('../api/src/routes/app/cost/general/routeExpenses.php');
require_once('../api/src/routes/app/cost/general/routeExpensesDistribution.php');
require_once('../api/src/routes/app/cost/general/routeExpenseRecover.php');
require_once('../api/src/routes/app/cost/general/routePayroll.php');
require_once('../api/src/routes/app/cost/general/routeProcessPayroll.php');
require_once('../api/src/routes/app/cost/general/routePuc.php');


/* Global */
require_once('../api/src/routes/app/global/routeCompany.php');
require_once('../api/src/routes/app/global/routeDoubleFactor.php');

// Profile
require_once('../api/src/routes/app/global/routeProfile.php');

/* Login */
require_once('../api/src/routes/app/login/routeLogin.php');
require_once('../api/src/routes/app/login/routepassUser.php');

// Prices
require_once('../api/src/routes/app/cost/prices/routePrices.php');

// Tools
require_once('../api/src/routes/app/cost/tools/routeSupport.php');

// Economy Scales
require_once('../api/src/routes/app/cost/economyScale/routeEconomyScale.php');

// User Access
require_once('../api/src/routes/app/cost/userAccess/routeUserAccess.php');

/* User */
require_once('../api/src/routes/app/users/routeGeneralUserAccess.php');
require_once('../api/src/routes/app/users/routeUsers.php');
require_once('../api/src/routes/app/users/routeQuantityUsers.php');
require_once('../api/src/routes/app/users/routeUsersStatus.php');


/* App Planning */
// Basic
require_once('../api/src/routes/app/planning/basic/routeInvMolds.php');
require_once('../api/src/routes/app/planning/basic/routeMachines.php');
require_once('../api/src/routes/app/planning/basic/routeMaterials.php');
require_once('../api/src/routes/app/planning/basic/routeProducts.php');
require_once('../api/src/routes/app/planning/basic/routeProcess.php');
// Classification
require_once('../api/src/routes/app/planning/classification/routeClassification.php');
// Config
require_once('../api/src/routes/app/planning/config/routeProductsMaterials.php');
require_once('../api/src/routes/app/planning/config/routeProductsInProcess.php');
require_once('../api/src/routes/app/planning/config/routeProductsProcess.php');
require_once('../api/src/routes/app/planning/config/routePlanning_machines.php');
require_once('../api/src/routes/app/planning/config/routePlanCiclesMachine.php');
// General
require_once('../api/src/routes/app/planning/general/routeCategories.php');
require_once('../api/src/routes/app/planning/general/routeUnitsSales.php');
// Administrador
require_once('../api/src/routes/app/planning/admin/routeUserAccess.php');
require_once('../api/src/routes/app/planning/admin/routeClients.php');
require_once('../api/src/routes/app/planning/admin/routeOrderTypes.php');

// Inventario
require_once('../api/src/routes/app/planning/inventory/routeInventory.php');
// Pedidos
require_once('../api/src/routes/app/planning/order/routeOrders.php');
// Programa
require_once('../api/src/routes/app/planning/program/routeProgramming.php');
require_once('../api/src/routes/app/planning/program/routeConsolidated.php');


$app->run();
