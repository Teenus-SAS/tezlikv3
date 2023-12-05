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
require_once('../api/src/routes/admin/plans/routePlanAccess.php');

// Login
require_once('../api/src/routes/admin/login/routeLastLoginsUsers.php');

// Products
require_once('../api/src/routes/admin/products/routeQuantityProducts.php');

// Puc
require_once('../api/src/routes/admin/puc/routePucs.php');

// Notifications
require_once('../api/src/routes/admin/notifications/routeNotifications.php');
// Users
require_once('../api/src/routes/admin/users/routeActiveUsers.php');
require_once('../api/src/routes/admin/users/routeCloseSessionUsers.php');
require_once('../api/src/routes/admin/users/routeUserAdmin.php');
require_once('../api/src/routes/app/login/routeInactiveUser.php');

// Benefits
require_once('../api/src/routes/admin/benefits/routeBenefits.php');

// Risks
require_once('../api/src/routes/admin/risks/routeRisks.php');

// Trm
require_once('../api/src/routes/admin/trm/routeTrm.php');

// Units
require_once('../api/src/routes/admin/units/routeUnits.php');
// Magnitudes
require_once('../api/src/routes/admin/magnitude/routeMagnitude.php');

// Binnacle
require_once('../api/src/routes/admin/binnacle/routeBinnacle.php');

// Dashboard
require_once('../api/src/routes/admin/dashboard/routeDashboardGeneral.php');

// Contract
require_once('../api/src/routes/admin/contract/routeContract.php');

/* App Cost */
// Trm
require_once('../api/src/routes/app/cost/trm/routeTrm.php');

// Quotes
require_once('../api/src/routes/app/cost/quotes/routeQuotes.php');
require_once('../api/src/routes/app/cost/quotes/routeCompanies.php');
require_once('../api/src/routes/app/cost/quotes/routeContacts.php');
require_once('../api/src/routes/app/cost/quotes/routePaymentMethods.php');

// Basic
require_once('../api/src/routes/app/cost/basic/routeProcess.php');
require_once('../api/src/routes/app/cost/basic/routeMachines.php');
require_once('../api/src/routes/app/cost/basic/routeMaterials.php');
require_once('../api/src/routes/app/cost/basic/routeProducts.php');

// Config
require_once('../api/src/routes/app/cost/config/routeExternalServices.php');
require_once('../api/src/routes/app/cost/config/routeFactoryLoad.php');
require_once('../api/src/routes/app/cost/config/routeProductsProcess.php');
require_once('../api/src/routes/app/cost/config/routeCompositesProducts.php');
require_once('../api/src/routes/app/cost/config/routeProductsMaterials.php');

// Dashboard
require_once('../api/src/routes/app/cost/dashboard/routeDashboardGenerals.php');
require_once('../api/src/routes/app/cost/dashboard/routeDashboardProducts.php');

// General
require_once('../api/src/routes/app/cost/general/routeExpenses.php');
require_once('../api/src/routes/app/cost/general/expensesDistribution/routeExpensesDistribution.php');
require_once('../api/src/routes/app/cost/general/expensesDistribution/routeFamilies.php');
require_once('../api/src/routes/app/cost/general/routeExpenseRecover.php');
require_once('../api/src/routes/app/cost/general/routePayroll.php');
require_once('../api/src/routes/app/cost/general/routeProcessPayroll.php');
require_once('../api/src/routes/app/cost/general/routePuc.php');
require_once('../api/src/routes/app/cost/general/routePriceList.php');

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
require_once('../api/src/routes/app/cost/prices/routePricesUSD.php');
require_once('../api/src/routes/app/cost/prices/customPrices/routesCustomPrices.php');
require_once('../api/src/routes/app/cost/prices/customPrices/routeCustomPercentage.php');

// support
require_once('../api/src/routes/app/cost/support/routeSupport.php');

// Tools
require_once('../api/src/routes/app/cost/tools/routeEconomyScale.php');
require_once('../api/src/routes/app/cost/tools/routeMultiproducts.php');
require_once('../api/src/routes/app/cost/tools/analysisMaterials/routeProducts.php');
require_once('../api/src/routes/app/cost/tools/analysisMaterials/routeLots.php');
require_once('../api/src/routes/app/cost/tools/routeSimulator.php');
require_once('../api/src/routes/app/cost/tools/routeHistorical.php');

// User Access
require_once('../api/src/routes/app/cost/userAccess/routeUserAccess.php');

/* User */
require_once('../api/src/routes/app/users/routeGeneralUserAccess.php');
require_once('../api/src/routes/app/users/routeUsers.php');
require_once('../api/src/routes/app/users/routeQuantityUsers.php');
require_once('../api/src/routes/app/users/routeUsersStatus.php');

$app->run();
