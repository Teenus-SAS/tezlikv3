<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/router.php");

// Login
get('/', '/index.php');

//Global
get('/profile', '/global/views/profile/profile.php');
get('/forgot-pass', '/global/views/login/forgot-password.php');
get('/reset-pass', '/global/views/login/reset-password.php');

//COST
//basic

get('/products', '/cost/views/basic/createProducts.php');
get('/materials', '/cost/views/basic/createRawMaterials.php');
get('/machines', '/cost/views/basic/createMachines.php');
get('/process', '/cost/views/basic/createProcess.php');

//Planning
get('/planning', '/planning/views/templatePlanning.php');
get('/planning/molds', '/planning/views/basic/invMolds.php');
get('/planning/products', '/planning/views/basic/createProducts.php');





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
