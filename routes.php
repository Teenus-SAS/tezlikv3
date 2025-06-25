<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/router.php");

// Login
get('/', '/index.php');

//Global
get('/forgot-pass', '/public/views/login/forgot-password.php');
get('/reset-pass', '/public/views/login/reset-password.php');

/* ADMIN */
//Navbar
get('/admin', '/admin/index.php');
get('/admin/companies', '/admin/views/companies/companies.php');
get('/admin/companies-licences', '/admin/views/companies/companiesLicenses.php');
get('/admin/historical-trm', '/admin/views/trm/historicalTrm.php');
get('/admin/users-log', '/admin/views/users/usersLog.php');
get('/admin/puc', '/admin/views/puc/puc.php');
get('/admin/notifications', '/admin/views/notifications/notifications.php');
get('/admin/companies-user', '/admin/views/companies/companyUsers.php');
//Header
get('/admin/users-admins', '/admin/views/users/usersAdmins.php');
get('/admin/users', '/admin/views/users/users.php');
get('/admin/plans', '/admin/views/plans/plans.php');
get('/admin/benefits', '/admin/views/benefits/benefits.php');
get('/admin/risks', '/admin/views/risks/risks.php');
get('/admin/magnitudes', '/admin/views/magnitudes/magnitudes.php');
get('/admin/units', '/admin/views/units/units.php');
get('/admin/binnacle', '/admin/views/binnacle/binnacle.php');
get('/admin/profile', '/admin/views/perfil/perfil.php');
get('/admin/contract', '/admin/views/contract/contract.php');


/* COST */

//Navbar
get('/cost', '/cost/index.php');
get('/cost/prices', '/cost/views/prices/pricesCOP/prices.php');
get('/cost/portfolio', '/cost/views/tools/portfolio/portfolio.php');
get('/cost/details-prices', '/cost/views/prices/pricesCOP/detailsPrices.php');
get('/cost/analysis-materials-product', '/cost/views/tools/analysisMaterials/products.php');
get('/cost/analysis-materials-lot', '/cost/views/tools/analysisMaterials/lots.php');
get('/cost/multiproduct', '/cost/views/tools/multiproduct.php');
get('/cost/support', '/cost/views/support/emailSupport.php');
get('/cost/quotes', '/cost/views/quotes/quotes.php');
get('/cost/prices-usd', '/cost/views/prices/pricesUSD/pricesUSD.php');
get('/cost/custom-prices', '/cost/views/prices/customPrices/customPrices.php');
get('/cost/details-quote', '/cost/views/quotes/detailsQuote.php');
get('/cost/efficientNegotiations', '/cost/views/tools/economyScale/efficientNegotiations.php');
get('/cost/saleObjectives', '/cost/views/tools/economyScale/saleObjectives.php');
get('/cost/priceObjectives', '/cost/views/tools/economyScale/priceObjectives.php');
get('/cost/simulator', '/cost/views/tools/simulator.php');
get('/cost/historical', '/cost/views/tools/historical/historical.php');
get('/cost/details-historical', '/cost/views/tools/historical/detailsHistorical.php');
get('/cost/profile', '/cost/views/perfil/perfil.php');
get('/cost/configuration', '/cost/views/perfil/configuracion.php');
//Header
//basic
get('/cost/products', '/cost/views/basic/createProducts.php');
get('/cost/materials', '/cost/views/basic/createRawMaterials.php');
get('/cost/machines', '/cost/views/basic/createMachines.php');
get('/cost/process', '/cost/views/basic/createProcess.php');
//Config
get('/cost/product-materials', '/cost/views/config/productMaterials.php');
get('/cost/product-process', '/cost/views/config/productProcess.php');
get('/cost/factory-load', '/cost/views/config/factoryLoad.php');
get('/cost/external-services', '/cost/views/config/externalServices.php');
//General
get('/cost/payroll', '/cost/views/general/createPayroll.php');
get('/cost/general-expenses', '/cost/views/general/expensesAssignation.php');
get('/cost/expenses-distribution', '/cost/views/general/expensesDistribution.php');
get('/cost/price-list', '/cost/views/general/PriceList.php');
// Quotes
//basic
get('/cost/companies', '/cost/views/quotes/companies.php');
get('/cost/contacts', '/cost/views/quotes/contacts.php');
get('/cost/payment-methods', '/cost/views/quotes/paymentMethods.php');

//Admin
get('/cost/users', '/cost/views/admin/users.php');
