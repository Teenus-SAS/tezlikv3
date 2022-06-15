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
require_once('../api/src/routes/admin/puc/routePUC.php');

// Users
require_once('../api/src/routes/admin/users/routeActiveUsers.php');
require_once('../api/src/routes/admin/users/routeCloseSessionUsers.php');

// Dashboard
require_once('../api/src/routes/admin/dashboard/routeDashboardGeneral.php');



/* App */

// Analysis
require_once('../api/src/routes/app/analysis/routeReviewRawMaterials.php');

// Basic
require_once('../api/src/routes/app/basic/routeMachines.php');
require_once('../api/src/routes/app/basic/routeMaterials.php');
require_once('../api/src/routes/app/basic/routeProcess.php');
require_once('../api/src/routes/app/basic/routeProducts.php');

// Config
require_once('../api/src/routes/app/config/routeExternalServices.php');
require_once('../api/src/routes/app/config/routeFactoryLoad.php');
require_once('../api/src/routes/app/config/routeProductsMaterials.php');
require_once('../api/src/routes/app/config/routeProductsProcess.php');

// Dashboard
require_once('../api/src/routes/app/dashboard/routeDashboardGenerals.php');
require_once('../api/src/routes/app/dashboard/routeDashboardProducts.php');

// Double factor
require_once('../api/src/routes/app/doubleFactor/routeDoubleFactor.php');

// General
require_once('../api/src/routes/app/general/routeExpenses.php');
require_once('../api/src/routes/app/general/routeExpensesDistribution.php');
require_once('../api/src/routes/app/general/routePayroll.php');
require_once('../api/src/routes/app/general/routeProcessPayroll.php');

// Global
require_once('../api/src/routes/app/global/routeCompany.php');
require_once('../api/src/routes/app/global/routePuc.php');

// Login
require_once('../api/src/routes/app/login/routeLogin.php');
require_once('../api/src/routes/app/login/routeLicenseCompany.php');
require_once('../api/src/routes/app/login/routepassUser.php');

// Prices
require_once('../api/src/routes/app/prices/routePrices.php');

// Tools
require_once('../api/src/routes/app/tools/routeSupport.php');

// User
require_once('../api/src/routes/app/users/routeUserAccess.php');
require_once('../api/src/routes/app/users/routeUsers.php');
require_once('../api/src/routes/app/users/routeQuantityUsers.php');
require_once('../api/src/routes/app/users/routeUsersStatus.php');


$app->run();
