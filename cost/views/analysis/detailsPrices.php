<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Details Prices</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Begin Header -->
        <?php include_once dirname(dirname(__DIR__)) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once dirname(dirname(__DIR__)) . '/partials/nav.php'; ?>

        <!-- Begin main content -->
        <div class="main-content">
            <!-- Content -->
            <div class="page-content">
                <!-- Page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="page-title">
                                    <div class="row justify-content-between">
                                        <div class="col-6">
                                            <h3 class="mb-1 font-weight-bold text-dark" id="nameProduct"></h3>
                                        </div>
                                        <div class="col-2 imageProduct">
                                        </div>
                                        <div class="col-4">
                                            <select id="product" class="form-control"></select>
                                        </div>
                                    </div>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Análisis de Costos</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Widget  -->
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Materia Prima</span>
                                                <h2 class="mb-0 mt-1 costProduct" id="rawMaterial"></h2>
                                            </div>
                                            <div class="text-center">
                                                <!-- <div id="t-rev"></div> -->
                                                <span class="text-info font-weight-bold" style="font-size:large">
                                                    <i class="" id="percentRawMaterial" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Mano de Obra</span>
                                                <h2 class="mb-0 mt-1" id="workforce"></h2>
                                            </div>
                                            <div class="text-center">
                                                <!-- <div id="t-order"></div> -->
                                                <span class="text-info font-weight-bold" style="font-size:large">
                                                    <i class="" id="percentWorkforce" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Costos Indirectos</span>
                                                <h2 class="mb-0 mt-1 number" id="indirectCost"></h2>
                                            </div>
                                            <div class="text-center">
                                                <!-- <div id="t-user"></div> -->
                                                <span class="text-info font-weight-bold" style="font-size:large">
                                                    <i class="" id="percentIndirectCost" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Gastos Generales</span>
                                                <h2 class="mb-0 mt-1 number" id="assignableExpenses"></h2>
                                            </div>
                                            <div class="text-center">
                                                <!-- <div id="t-visitor"></div> -->
                                                <span class="text-info font-weight-bold" style="font-size:large">
                                                    <i class="" id="percentAssignableExpenses" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2-->
                        <div class="row align-items-stretch">
                            <div class="col-md-4 col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Ventas</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Número de Unidades</p>
                                                        <h4 class="mb-0" id="unitsSold"></h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-info mr-0 align-self-center">
                                                        <i class="bx bx-layer fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Ingresos</p>
                                                        <h4 class="mb-0" id="turnover"></h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-primary mr-0 align-self-center">
                                                        <i class="bx bx-bar-chart-alt fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Precio de Venta Recomendado</p>
                                                        <h4 class="mb-0" id="recomendedPrice">$</h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-success mr-0 align-self-center">
                                                        <i class="bx bx-chart fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Begin total revenue chart -->
                            <div class="col-md-4 col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Costos y Gastos</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <!-- <div id="chartProductCosts"></div> -->
                                        <canvas id="chartProductCosts"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- End total revenue chart -->
                            <div class="col-md-4 col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Costeo Total</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <div style="display: grid;grid-template-columns:1fr 110px">
                                                            <p class="mb-2" style="color:green">Precio de Venta</p>
                                                            <h6 class="mb-0 pl-3 text-right" id="salesPrice"></h6>
                                                            <p class="mb-2" style="color:darkcyan">Total Costos</p>
                                                            <h6 class="mb-0 pl-3 text-right" id="costTotal"></h6>
                                                            <p class="mb-2" style="color:darkcyan">Costos</p>
                                                            <h6 class="mb-0 pl-3 text-right" id="cost"></h6>
                                                            <p class="text-muted mb-2 pl-3">Materia Prima</p>
                                                            <h6 class="mb-0 pl-3 text-right" id="payRawMaterial"></h6>
                                                            <p class="text-muted mb-2 pl-3">Mano de Obra</p>
                                                            <h6 class="mb-0 pl-3 text-right" id="payWorkforce">$</h6>
                                                            <p class="text-muted mb-2 pl-3">Costos Indirectos</p>
                                                            <h6 class="mb-0 pl-3 text-right " id="payIndirectCost">$</h6>
                                                            <p class="mb-2" style="color:darkcyan" id="expenses">Gastos</p>
                                                            <h6 class="mb-0 pl-3 text-right" id="payAssignableExpenses"></h6>
                                                            <p class="mb-2" style="color:darkcyan" id="commission">Comisión Vts</p>
                                                            <h6 class="mb-0 pl-3 text-right" id="commisionSale"></h6>
                                                            <p class="mb-2" style="color:darkcyan" id="profit">Rentabilidad</p>
                                                            <h6 class="mb-0 pl-3 text-right" id="profitability"></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 4-->
                        <div class="row">
                            <!-- Begin total sales chart -->
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Costo Mano de Obra</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartWorkForce" style="width: 90%;"></canvas>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Total Costo</p>
                                                <p class="mb-0 font-weight-bold" id="totalCostWorkforceEsp"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Total Tiempo Proceso</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <div class="chart">
                                                <canvas id="chartTimeProcess" style="width: 90%;"></canvas>
                                            </div>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Tiempo Total</p>
                                                <p class="mb-0 font-weight-bold" id="totalTimeProcess"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Total Tiempos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <div class="chart">
                                                <canvas id="chartManufactTime" style="width: 90%;"></canvas>
                                            </div>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Tiempo Total</p>
                                                <p class="mb-0 font-weight-bold" id="manufactPromTime"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Composición Precio </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <div class="chart">
                                                <canvas id="chartPrice" style="width: 90%;"></canvas>
                                            </div>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Precio Total</p>
                                                <p class="mb-0 font-weight-bold" id="totalPricesComp"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End total sales chart -->
                            <!-- Begin earning chart -->
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header dflex-between-center">
                                        <h5 class="card-title">Costos Materia Prima</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <canvas id="chartMaterialsCosts"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- End earning chart -->
                            <!-- Begin today sale -->
                            <!-- <div class="col-lg-3">
                                <div class="card revenue-card">
                                    <div class="card-header bg-info">
                                        <h5 class="card-title text-white">Revenue</h5>
                                    </div>
                                    <div class="card-body bg-info position-relative">
                                        <div class="chart-container">
                                            <div class="chart h-150">
                                                <canvas id="today-revenue"></canvas>
                                            </div>
                                        </div>
                                        <div class="center-text">
                                            <p class="text-light mb-1 font-weight-600">Sale </p>
                                            <h4 class="text-white mb-0 font-weight-bold">$600</h4>
                                        </div>
                                    </div>
                                    <div class="revenue-stats p-4">
                                        <div>
                                            <p class="text-muted">Target</p>
                                            <h4>$2000</h4>
                                        </div>
                                        <div>
                                            <p class="text-muted">Current</p>
                                            <h4>$1500</h4>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <!-- End today sale -->
                        </div>
                        <!-- Row 3-->
                        <!-- <div class="row"> -->
                        <!-- Begin recent orders -->
                        <!-- <div class="col-12 col-lg-8">
                            <div class="card">
                                <div class="card-header dflex-between-center">
                                    <h5 class="card-title">Recent Orders</h5>
                                    <div class="export-fnc">
                                        <button class="btn btn-primary btn-sm mr-3 ml-1" data-effect="wave">
                                            <i class="bx bx-export"></i> Export
                                        </button>
                                        <div class="arrow-pagination">
                                            <ul class="pagination mb-0">
                                                <li class="page-item disabled"><a class="page-link" data-effect="wave" href="javascript:void(0)"><i class="bx bx-chevron-left"></i></a></li>
                                                <li class="page-item"><a class="page-link" data-effect="wave" href="javascript:void(0)"><i class="bx bx-chevron-right"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-nowrap mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th>Customer</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>#1</td>
                                                    <td>Bicycle</td>
                                                    <td>Otto B</td>
                                                    <td>$124</td>
                                                    <td><span class="badge py-1 badge-soft-danger">Declined</span></td>
                                                </tr>
                                                <tr>
                                                    <td>#2</td>
                                                    <td>Addidas Shoes</td>
                                                    <td>Danny Johnson</td>
                                                    <td>$100</td>
                                                    <td><span class="badge py-1 badge-soft-warning">Pending</span></td>
                                                </tr>
                                                <tr>
                                                    <td>#3</td>
                                                    <td>Cut Sleeve Jacket</td>
                                                    <td>Alvin Newton</td>
                                                    <td>$50</td>
                                                    <td><span class="badge py-1 badge-soft-success">Delivered</span></td>
                                                </tr>
                                                <tr>
                                                    <td>#4</td>
                                                    <td>Half Shirt</td>
                                                    <td>Bennie Perez</td>
                                                    <td>$80</td>
                                                    <td><span class="badge py-1 badge-soft-success">Delivered</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- End recent orders -->
                        <!-- Begin quarter sale -->
                        <!-- <div class="col-12 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Quartly Sale</h5>
                                </div>
                                <div class="card-body pt-2">
                                    <div id="quartly-sale"></div>
                                </div>
                            </div>
                        </div> -->
                        <!-- End quarter sale -->
                        <!-- </div> -->

                        <!-- Row 5 -->
                        <!-- <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Inventory Stock</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Serial</th>
                                                        <th>Code</th>
                                                        <th>Date</th>
                                                        <th>Stock</th>
                                                        <th>Stock Left</th>
                                                        <th>Status</th>
                                                        <th>Ratings</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>#1</td>
                                                        <td><strong>8765482</strong></td>
                                                        <td>November 14, 2019</td>
                                                        <td>15000</td>
                                                        <td>10000</td>
                                                        <td><span class="badge badge-soft-success">In Stock</span></td>
                                                        <td>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star-half text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#2</td>
                                                        <td><strong>2366482</strong></td>
                                                        <td>November 15, 2019</td>
                                                        <td>15000</td>
                                                        <td>100</td>
                                                        <td><span class="badge badge-soft-danger">Out Stock</span></td>
                                                        <td>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star-half text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bx-star text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#3</td>
                                                        <td><strong>3557477</strong></td>
                                                        <td>November 16, 2019</td>
                                                        <td>15000</td>
                                                        <td>7000</td>
                                                        <td><span class="badge badge-soft-success">In Stock</span></td>
                                                        <td>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#4</td>
                                                        <td><strong>8747754</strong></td>
                                                        <td>November 17, 2019</td>
                                                        <td>15000</td>
                                                        <td>8000</td>
                                                        <td><span class="badge badge-soft-success">In Stock</span></td>
                                                        <td>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star-half text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bx-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bx-star text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#5</td>
                                                        <td><strong>9874745</strong></td>
                                                        <td>November 18, 2019</td>
                                                        <td>15000</td>
                                                        <td>50</td>
                                                        <td><span class="badge badge-soft-danger">Out Stock</span></td>
                                                        <td>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="javascript:void(0)"><i class="bx bxs-star-half text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="my-3 d-flex justify-content-end">
                                            <ul class="pagination  flat-rounded-pagination">
                                                <li class="page-item disabled">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave" aria-label="Previous" tabindex="-1" aria-disabled="true">
                                                        <i class="bx bx-chevron-left"></i>
                                                    </a>
                                                </li>
                                                <li class="page-item active" aria-current="page">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave">1</a>
                                                </li>
                                                <li class="page-item" aria-current="page">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave">2</a>
                                                </li>
                                                <li class="page-item" aria-current="page">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave">3</a>
                                                </li>
                                                <li class="page-item">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave" aria-label="Next">
                                                        <i class="bx bx-chevron-right"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content end -->

        <!-- Footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/cost/js/analysis/prices/configPrices.js"></script>
    <script src="/cost/js/dashboard/indicatorsProduct.js"></script>
    <script src="/cost/js/dashboard/graphicsProduct.js"></script>
</body>

</html>