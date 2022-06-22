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

// Login
require_once('../api/src/routes/admin/login/routeLastLoginsUsers.php');

// Products
require_once('../api/src/routes/admin/products/routeQuantityProducts.php');

// PUC
require_once('../api/src/routes/admin/puc/routePucs.php');

// Users
require_once('../api/src/routes/admin/users/routeActiveUsers.php');
require_once('../api/src/routes/admin/users/routeCloseSessionUsers.php');

// Dashboard
require_once('../api/src/routes/admin/dashboard/routeDashboardGeneral.php');


/* App Cost */

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
require_once('../api/src/routes/app/cost/general/routePayroll.php');
require_once('../api/src/routes/app/cost/general/routeProcessPayroll.php');


/* Global */
require_once('../api/src/routes/app/global/routeCompany.php');
require_once('../api/src/routes/app/global/routeDoubleFactor.php');
require_once('../api/src/routes/app/global/routePuc.php');

/* Login */
require_once('../api/src/routes/app/login/routeLicenseCompany.php');
require_once('../api/src/routes/app/login/routeLogin.php');
require_once('../api/src/routes/app/login/routepassUser.php');

// Prices
require_once('../api/src/routes/app/cost/prices/routePrices.php');

// Tools
require_once('../api/src/routes/app/cost/tools/routeSupport.php');

/* User */
require_once('../api/src/routes/app/users/routeUsers.php');
require_once('../api/src/routes/app/users/routeQuantityUsers.php');
require_once('../api/src/routes/app/users/routeUsersStatus.php');
require_once('../api/src/routes/app/users/routeUserAccess.php');


/* App Planning */
// Basic
require_once('../api/src/routes/app/planning/basic/routeMachines.php');
require_once('../api/src/routes/app/planning/basic/routeMaterials.php');
require_once('../api/src/routes/app/planning/basic/routeProducts.php');
// Config
require_once('../api/src/routes/app/planning/config/routePlanning_machines.php');
require_once('../api/src/routes/app/planning/config/routePlanCiclesMachine.php');

$app->run();
