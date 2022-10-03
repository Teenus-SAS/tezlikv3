<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/router.php");

// Login
get('/', '/index.php');

//Global
get('/forgot-pass', '/global/views/login/forgot-password.php');
get('/reset-pass', '/global/views/login/reset-password.php');

/* ADMIN */
get('/admin', '/admin/index.php');
get('/admin/companies', '/admin/views/companies/companies.php');
get('/admin/companies-licences', '/admin/views/companies/companiesLicenses.php');
get('/admin/companies-user', '/admin/views/companies/companyUsers.php');
get('/admin/notification', '/admin/views/notification/notification.php');
get('/admin/puc', '/admin/views/puc/puc.php');
get('/admin/users', '/admin/views/users/users.php');
get('/admin/users-log', '/admin/views/users/usersLog.php');
get('/admin/profile', '/admin/views/perfil/perfil.php');



/* COST */
//Navbar
get('/cost', '/cost/index.php');
get('/cost/prices', '/cost/views/analysis/prices.php');
get('/cost/details-prices', '/cost/views/analysis/detailsPrices.php');
get('/cost/analysis-materials', '/cost/views/analysis/materials.php');
get('/cost/support', '/cost/views/support/emailSupport.php');
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
//Admin
get('/cost/users', '/cost/views/users/users.php');

/* PLANNING */
get('/planning', '/planning/index.php');
get('/planning/inventory', '/planning/views/inventory/inventory.php');
get('/planning/orders', '/planning/views/orders/orders.php');
get('/planning/programming', '/planning/views/program/programming/programming.php');
get('/planning/consolidated', '/planning/views/program/consolidated/consolidated.php');
//Basic
get('/planning/molds', '/planning/views/basic/invMolds.php');
get('/planning/products', '/planning/views/basic/createProducts.php');
get('/planning/materials', '/planning/views/basic/createRawMaterials.php');
get('/planning/machines', '/planning/views/basic/createMachines.php');
get('/planning/process', '/planning/views/basic/createProcess.php');
//Config
get('/planning/product-materials', '/planning/views/config/productMaterials.php');
get('/planning/product-process', '/planning/views/config/productProcess.php');
get('/planning/cicles-machines', '/planning/views/config/planCiclesMachine.php');
get('/planning/planning-machines', '/planning/views/config/planningMachines.php');
//General
get('/planning/categories', '/planning/views/general/categories.php');
get('/planning/sales', '/planning/views/general/sales.php');
//Admin
get('/planning/clients', '/planning/views/admin/clients.php');
get('/planning/order-types', '/planning/views/admin/order_types.php');
get('/planning/users', '/planning/views/admin/users.php');
get('/planning/profile', '/planning/views/perfil/perfil.php');


/* SELECTOR */
get('/selector', '/selector/index.php');
get('/selector/users', '/selector/views/users/users.php');

// Dynamic GET. Example with 1 variable
// The $id will be available in user.php
//get('/user/$id', 'user.php');

// Dynamic GET. Example with 2 variables
// The $name will be available in user.php
// The $last_name will be available in user.php
//get('/user/$name/$last_name', 'user.php');

// Dynamic GET. Example with 2 variables with static
// In the URL -> http://localhost/product/shoes/color/blue
// The $type will be available in product.php
// The $color will be available in product.php
//get('/product/$type/color/:color', 'product.php');

// Dynamic GET. Example with 1 variable and 1 query string
// In the URL -> http://localhost/item/car?price=10
// The $name will be available in items.php which is inside the views folder
//get('/item/$name', 'views/items.php');

// any can be used for GETs or POSTs

// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
//any('/404','views/404.php');
