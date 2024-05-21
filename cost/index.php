<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php require_once __DIR__ . '/modals/modalGeneralDashboard.php'; ?>
<?php require_once __DIR__ . '/modals/autoHistorical.php'; ?>
<?php require_once __DIR__ . '/modals/FirstLogin.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="TezlikSoftware">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>Tezlik | Dashboard</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(__DIR__) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Loader -->
        <div class="loading">
            <a href="javascript:;" class="close-btn"><i class="bi bi-x-circle-fill"></i></a>
            <div class="loader"></div>
        </div>

        <!-- Begin Header -->
        <?php include_once (__DIR__) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once (__DIR__) . '/partials/nav.php'; ?>

        <!-- Begin main content -->
        <div class="main-content">
            <!-- content -->
            <div class="page-content" id="invoice">
                <?php if ($_SESSION['license_days'] <= 30) { ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-danger alert-dismissible fade show text-center" style="margin-bottom: 0px;" role="alert">
                                <strong>¡Pronto se acabara tu licencia (<?php echo $_SESSION['license_days']; ?> días)! .</strong> Comunícate con tu administrador para mas información.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($_SESSION['license_days'] > 30 && $_SESSION['license_days'] < 40) { ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-warning alert-dismissible fade show text-center" style="margin-bottom: 0px;" role="alert">
                                <strong>¡Pronto se acabara tu licencia (<?php echo $_SESSION['license_days']; ?> días)! .</strong> Comunícate con tu administrador para mas información.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6 cardHeader">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Dashboard Consolidado</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Bienvenido</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="col-sm-5 col-xl-6 d-flex justify-content-end btnPrintPDF">
                                <div class="col-xs-2 mt-2 mr-2" id="btnPdf">
                                    <a href="javascript:;" <i class="bi bi-filetype-pdf" data-toggle='tooltip' onclick="printPDF(1)" style="font-size: 30px; color:red;"></i></a>
                                </div>
                                <?php
                                // $_SESSION['price_usd'] == 1 &&
                                if ($_SESSION['flag_currency_usd'] == 1 || $_SESSION['flag_currency_eur'] == 1) { ?>
                                    <div class="col-xs-2 mr-2 mt-1">
                                        <label class="ml-3 text-dark">Tipo moneda</label>
                                        <select class="form-control" id="selectCurrency">
                                            <option disabled selected>Seleccionar</option>
                                            <option value="1">COP</option>
                                            <?php if ($_SESSION['flag_currency_usd'] == 1) { ?>
                                                <option value="2">USD</option>
                                            <?php } ?>
                                            <?php if ($_SESSION['flag_currency_eur'] == 1) { ?>
                                                <option value="3">EUR</option>
                                            <?php } ?>
                                        </select>
                                        <!-- <button class="btn btn-info btnPricesUSD" id="usd">Precios USD</button> -->
                                    </div>
                                    <div class="col-xs-2 ml-2 form-group floating-label enable-floating-label cardUSD" style="display:none; margin-top:35px;">
                                        <label class="font-weight-bold text-dark">Valor Dolar</label>
                                        <input type="text" style="background-color: aliceblue;" class="form-control text-center calcInputs" name="valueCoverage" id="valueCoverage" value="<?php
                                                                                                                                                                                            $coverage = sprintf('$ %s', number_format($_SESSION['coverage'], 2, ',', '.'));
                                                                                                                                                                                            echo  $coverage ?>" readonly>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Widget  -->
                        <?php if ($_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1) { ?>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5">
                            <?php } else { ?>
                                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
                                <?php } ?>

                                <?php if (($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) && ($_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1)) { ?>
                                    <div class="col-xl-2"> <!-- Products -->
                                    <?php } ?>
                                    <?php if ($_SESSION['flag_expense'] == 2 || ($_SESSION['cost_multiproduct'] == 0 || $_SESSION['plan_cost_multiproduct'] == 0)) { ?>
                                        <div class="col"> <!-- Products -->
                                        <?php } ?>
                                        <div class="card radius-10 border-start border-0 border-3 border-info">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Productos</span>
                                                        <h2 class="mb-0 mt-1 text-info" id="products"></h2>
                                                    </div>
                                                    <div class="text-center">
                                                        <div id="t-rev"></div>
                                                        <span class="text-info font-weight-bold font-size-23">
                                                            <i class='bi bi-box-fill fs-xl'></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>

                                        <?php if (($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) && ($_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1)) { ?>
                                            <div class="col-xl-2"> <!-- Materials -->
                                            <?php } ?>
                                            <?php if ($_SESSION['flag_expense'] == 2 || ($_SESSION['cost_multiproduct'] == 0 || $_SESSION['plan_cost_multiproduct'] == 0)) { ?>
                                                <div class="col"> <!-- Materials -->
                                                <?php } ?>

                                                <div class="card radius-10 border-start border-0 border-3 border-info">
                                                    <div class="card-body">
                                                        <div class="media align-items-center">
                                                            <div class="media-body">
                                                                <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                                                    <span class="text-muted text-uppercase font-size-12 font-weight-bold">Mat Primas</span>
                                                                <?php } ?>
                                                                <?php if ($_SESSION['flag_expense'] == 2) { ?>
                                                                    <span class="text-muted text-uppercase font-size-12 font-weight-bold">Materias Primas</span>
                                                                <?php } ?>
                                                                <h2 class="mb-0 mt-1 text-info" id="materials"></h2>
                                                            </div>
                                                            <div class="text-center">
                                                                <div id="t-rev"></div>
                                                                <span class="text-info font-weight-bold font-size-13">
                                                                    <i class='bi bi-gear fs-xl'></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>

                                                <div class="col"> <!-- commision sales -->
                                                    <div class="card radius-10 border-start border-0 border-3 border-success">
                                                        <div class="card-body">
                                                            <div class="media align-items-center">
                                                                <div class="media-body">
                                                                    <span class="text-muted text-uppercase font-size-12 font-weight-bold">Comisión de Vta</span>
                                                                    <h2 class="mb-0 mt-1 text-success" id="comissionAverage"></h2>
                                                                </div>
                                                                <div class="text-center">
                                                                    <div id="t-user"></div>
                                                                    <span class="text-success font-weight-bold font-size-13">
                                                                        <i class='bi bi-cash fs-xl'></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php if (($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) && ($_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1)) { ?>
                                                    <div class="col-xl-3"> <!-- Expenses -->
                                                    <?php } ?>
                                                    <?php if ($_SESSION['flag_expense'] == 2 || ($_SESSION['cost_multiproduct'] == 0 || $_SESSION['plan_cost_multiproduct'] == 0)) { ?>
                                                        <div class="col"> <!-- Expenses -->
                                                        <?php } ?>

                                                        <div class="card radius-10 border-start border-0 border-3 border-info">
                                                            <div class="card-body">
                                                                <div class="media align-items-center">
                                                                    <div class="media-body">
                                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold" id="expenses1"></span>
                                                                        <h2 class="mb-0 mt-1 text-info" id="generalCost"></h2>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div id="t-visitor"></div>
                                                                        <span class="text-danger font-weight-bold font-size-13">
                                                                            <i class='bx bxs-pie-chart-alt-2 fs-xl'></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <?php if ($_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1) { ?>
                                                            <div class="col">
                                                                <div class="card radius-10 border-start border-0 border-3 border-warning" style="height: 92px;">
                                                                    <div class="card-body">
                                                                        <div class="media align-items-center" style="height: 60px;">
                                                                            <div class="media-body">
                                                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">PTO EQUILIBRIO</span>
                                                                                <h2 class="mb-0 mt-1 text-warning" id="multiproducts"></h2>
                                                                            </div>
                                                                            <div class="chart-container" style="height:90px; width:90px">
                                                                                <canvas id="chartMultiproducts"></canvas>
                                                                                <div class="center-text">
                                                                                    <h4 style="font-size: small;width: 60px;margin-left: 15px;" class="mb-0 font-weight-bold" id="percentageMultiproducts"></h4>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>

                                                    <!-- Row 2-->
                                                    <div class="row align-items-stretch">
                                                        <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                                            <div class="pt-4 col-md-4 col-lg-3">
                                                            <?php } ?>
                                                            <?php if ($_SESSION['flag_expense'] == 2) { ?>
                                                                <div class="col-md-4 col-lg-3">
                                                                <?php } ?>
                                                                <div class="cardActualProfitability"></div>
                                                                <div class="card bg-warning">
                                                                    <div class="card-body">
                                                                        <div class="media text-white">
                                                                            <div class="media-body">
                                                                                <?php if ($_SESSION['id_company'] == '10') { ?>
                                                                                    <span class="text-uppercase font-size-12 font-weight-bold">Margen Mínimo Deseado</span>
                                                                                <?php } else { ?>
                                                                                    <span class="text-uppercase font-size-12 font-weight-bold">Rentabilidad Mínima Deseada</span>
                                                                                <?php } ?>
                                                                                <h2 class="mb-0 mt-1 text-white" id="minProfitabilityAverage"></h2>
                                                                            </div>
                                                                            <div class="align-self-center mt-1">
                                                                                <i class="bx bx-bar-chart-alt fs-xl"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- Tiempos -->
                                                                <div class="card">
                                                                    <div class="card-body p-0">
                                                                        <ul class="list-group list-group-flush">
                                                                            <?php if ($_SESSION['flag_expense'] == 2 || $_SESSION['flag_expense'] == 0) { ?>
                                                                                <li class="list-group-item py-4" style="border-radius: 10px 10px 0 0;">
                                                                                    <div class="media">
                                                                                        <div class="media-body">
                                                                                            <p class="text-muted mb-2">Tiempo Alistamiento (Prom)</p>
                                                                                            <h4 class="mb-0 number" id="enlistmentTime"></h4>
                                                                                        </div>
                                                                                        <div class="avatar avatar-md bg-info mr-0 align-self-center">
                                                                                            <i class="bx bxs-time fs-lg"></i>
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                                <li class="list-group-item py-4">
                                                                                    <div class="media">
                                                                                        <div class="media-body">
                                                                                            <p class="text-muted mb-2">Tiempo Operación (Prom)</p>
                                                                                            <h4 class="mb-0 number" id="operationTime"></h4>
                                                                                        </div>
                                                                                        <div class="avatar avatar-md bg-primary mr-0 align-self-center">
                                                                                            <i class="bx bxs-time-five fs-lg"></i>
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                            <?php } ?>
                                                                            <li class="list-group-item py-4" style="border-radius: 0 0 10px 10px;">
                                                                                <div class="media">
                                                                                    <div class="media-body">
                                                                                        <p class="text-muted mb-2">Tiempo Promedio Fabricación</p>
                                                                                        <h4 class="mb-0" id="averageTotalTime"></h4>
                                                                                    </div>
                                                                                    <div class="avatar avatar-md bg-danger mr-0 align-self-center">
                                                                                        <i class='bx bx-error-circle fs-lg'></i>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                                <!-- Begin total revenue chart -->
                                                                <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                                                    <div class="col-xl-6"> <!-- Expenses -->
                                                                    <?php } ?>
                                                                    <?php if ($_SESSION['flag_expense'] == 2) { ?>
                                                                        <div class="col-xl-9"> <!-- Expenses -->
                                                                        <?php } ?>
                                                                        <!-- Prod mayor rentabilidad  -->
                                                                        <div class="card">
                                                                            <div class="card-header dflex-between-center">
                                                                                <h5 class="card-title productTitle">Productos con mayor rentabilidad (Sugerida)</h5>
                                                                                <div class="text-center">
                                                                                    <div class="btn-group">
                                                                                        <button class="btn btn-sm btn-warning" id="btnGraphicProducts" style="width: 40px; height: 40px; padding: 10px 16px; border-radius: 35px; font-size: 24px; line-height: 1.33; margin-right: 8px;">
                                                                                            <i class="bi bi-bar-chart-fill" style="margin-left:-8px"></i>
                                                                                        </button>
                                                                                        <button class="btn btn-sm btn-primary typePrice" id="sugered" value="1">Sugerido</button>
                                                                                        <button class="btn btn-sm btn-outline-primary typePrice" id="actual" value="2">Actual</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-body chart-container">
                                                                                <canvas id="chartProductsCost" class="chart"></canvas>
                                                                            </div>
                                                                        </div>
                                                                        </div>
                                                                        <?php if ($_SESSION['flag_expense'] != 2) { ?>
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
                                                                                                        <p class="text-muted mb-2">Total Unidades Vendidas</p>
                                                                                                        <h4 class="mb-0" id="productsSold"></h4>
                                                                                                    </div>
                                                                                                    <div class="avatar avatar-md bg-info mr-0 align-self-center">
                                                                                                        <i class="bx bx-layer fs-lg"></i>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </li>
                                                                                            <li class="list-group-item py-4">
                                                                                                <div class="media">
                                                                                                    <div class="media-body">
                                                                                                        <p class="text-muted mb-2">Total Ingresos por Ventas</p>
                                                                                                        <h4 class="mb-0" id="salesRevenue"></h4>
                                                                                                    </div>
                                                                                                    <div class="avatar avatar-md bg-primary mr-0 align-self-center">
                                                                                                        <i class="bx bx-bar-chart-alt fs-lg"></i>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </li>
                                                                                            <li class="list-group-item py-4">
                                                                                                <div class="media">
                                                                                                    <div class="media-body">
                                                                                                        <p class="text-muted mb-2">Costo de Ventas (Total)</p>
                                                                                                        <h4 class="mb-0" id="totalCostED"></h4>
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
                                                                        <?php } ?>
                                                                    </div>

                                                                    <!-- End total revenue chart -->

                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <div class="card">
                                                                                <div class="card-header">
                                                                                    <h5 class="card-title">Costo Mano de Obra (Min)</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <canvas id="chartWorkForceGeneral" style="width: 80%;"></canvas>
                                                                                    <div class="center-text">
                                                                                        <p class="text-muted mb-1 font-weight-600">Total Costo </p>
                                                                                        <h4 class="mb-0 font-weight-bold" id="totalCostWorkforce"></h4>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                            <div class="card">
                                                                                <div class="card-header">
                                                                                    <h5 class="card-title">Costo Carga Fabril</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div class="chart-container">
                                                                                        <canvas id="chartFactoryLoadCost" style="width: 80%;"></canvas>
                                                                                        <div class="center-text">
                                                                                            <p class="text-muted mb-1 font-weight-600">Tiempo Total</p>
                                                                                            <h4 class="mb-0 font-weight-bold" id="factoryLoadCost"></h4>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-4 pageBreak">
                                                                            <div class="card">
                                                                                <div class="card-header">
                                                                                    <h5 class="card-title">Gastos Generales</h5>
                                                                                </div>
                                                                                <div class="card-body pt-2">
                                                                                    <div class="chart-container">
                                                                                        <canvas id="chartExpensesGenerals" style="width: 80%;"></canvas>
                                                                                        <div class="center-text">
                                                                                            <p class="text-muted mb-1 font-weight-600">Total Gastos </p>
                                                                                            <h4 class="mb-0 font-weight-bold" id="totalCost"></h4>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12" style="height: fit-content;">
                                                                            <div class=" card">
                                                                                <div class="card-header">
                                                                                    <h5 class="card-title">Tiempo Total de Fabricación por Producto (min)</h5>
                                                                                </div>
                                                                                <div class="card-body pt-2">
                                                                                    <canvas id="chartTimeProcessProducts" style="width: 80%;"></canvas>
                                                                                    <div class="center-text">
                                                                                        <p class="text-muted mb-1 font-weight-600"></p>
                                                                                        <h4 class="mb-0 font-weight-bold"></h4>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12 cardTblMultiproducts" style="display: none;">
                                                                            <div class="card">
                                                                                <div class="card-header row">
                                                                                    <h5 class="col-sm-10 card-title">Multiproductos</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-striped text-center" id="tblMultiproducts">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th style="width: 350px">Producto</th>
                                                                                                    <th>No Unidades Vendidas</th>
                                                                                                    <th style="width: 150px;" id="lblPrice">Precio</th>
                                                                                                    <!-- <th style="width: 150px;">Costo Variable</th> -->
                                                                                                    <th style="width: 150px;">Participacion</th>
                                                                                                    <th>Margen De Contribucion</th>
                                                                                                    <!-- <th>Promedio Ponderado</th> -->
                                                                                                    <th>Unidades A Vender</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody id="tblMultiproductsBody"></tbody>
                                                                                            <tfoot>
                                                                                                <tr>
                                                                                                    <td>Total:</td>
                                                                                                    <td id="totalSoldsUnits"></td>
                                                                                                    <!-- <td></td> -->
                                                                                                    <td></td>
                                                                                                    <td id="totalParticipation"></td>
                                                                                                    <td></td>
                                                                                                    <!-- <td id="totalAverages"></td> -->
                                                                                                    <td id="totalSumUnits"></td>
                                                                                                </tr>
                                                                                            </tfoot>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                    </div>
                                            </div>
                                    </div>
                                    <!-- main content End -->

                                    <!-- footer -->
                                    <?php include_once  dirname(__DIR__) . '/global/partials/footer.php'; ?>
                                </div>
                                <!-- Page End -->

                                <?php include_once dirname(__DIR__) . '/global/partials/scriptsJS.php'; ?>

                                <script>
                                    flag_expense = "<?= $_SESSION['flag_expense'] ?>";

                                    // price_usd = 
                                    flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
                                    flag_currency_eur = "<?= $_SESSION['flag_currency_eur'] ?>";
                                    flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
                                    cost_multiproduct = "<?= $_SESSION['cost_multiproduct'] ?>";
                                    plan_cost_multiproduct = "<?= $_SESSION['plan_cost_multiproduct'] ?>";
                                    coverage = "<?= $_SESSION['coverage'] ?>";
                                    contract = "<?= $_SESSION['contract'] ?>";
                                    d_contract = "<?= $_SESSION['d_contract'] ?>";
                                    date_contract = "<?= $_SESSION['date_contract'] ?>";
                                    id_company = "<?= $_SESSION['id_company'] ?>";
                                    c_content = <?= json_encode($_SESSION['content']) ?>;
                                    DatatableTblMultiproducts = 0;
                                    type = 'auto';
                                    modalActive = false;

                                    // price_usd == '1' &&
                                    if (flag_currency_usd == '1' || flag_currency_eur == '1')
                                        $(document).ready(function() {
                                            // Validar que el valor de precio estaba anteriormente seleccionado
                                            let typeCurrency = sessionStorage.getItem('typeCurrency') || '1';
                                            $('.cardUSD').hide(800);

                                            switch (typeCurrency) {
                                                case '1': // Pesos COP
                                                    $('#selectCurrency').val('1');
                                                    break;
                                                case '2': // Dólares  
                                                    // document.getElementById('btnPdf').className = 'col-xs-2 mr-2';
                                                    // document.getElementById('btnPdf').style.marginTop = '40px';
                                                    $('#selectCurrency').val('2');

                                                    $('.cardUSD').show(800);
                                                    break;
                                                case '3': // Euros
                                                    $('#selectCurrency').val('3');
                                                    break;
                                            }

                                            // Funcion para cambiar de valor del precio manualmente
                                            $(document).on('change', '#selectCurrency', function() {
                                                let currency = this.value;
                                                $('.cardUSD').hide(800);
                                                // document.getElementById('btnPdf').style.marginTop = '';
                                                // document.getElementById('btnPdf').className = 'col-xs-2 mt-2 mr-2';

                                                sessionStorage.setItem('typeCurrency', currency);
                                                // Dolares
                                                if (currency == 2) {
                                                    // document.getElementById('btnPdf').className = 'col-xs-2 mr-2';
                                                    // document.getElementById('btnPdf').style.marginTop = '30px';

                                                    $('.cardUSD').show(800);
                                                }
                                                // } else { // Pesos
                                                //     $(`#selectCurrency option[value=1]`).prop("selected", true);
                                                //     // element.style.marginTop = '';
                                                //     // document.getElementById('btnPdf').style.marginTop = '';

                                                //     $('.cardUSD').hide(800);
                                                // }

                                                // Recargar Datos
                                                let id_product = $('#refProduct').val();

                                                loadAllData();
                                            });
                                        });
                                </script>
                                <script src="js/dashboard/contract.js"></script>
                                <script src="js/dashboard/indicatorsGeneral.js"></script>
                                <script src="js/dashboard/calcDataCost.js"></script>
                                <script src="js/dashboard/graphicsGeneral.js"></script>
                                <script src="js/dashboard/generalModals.js"></script>
                                <script src="/cost/js/tools/multiproduct/tblMultiproducts.js"></script>
                                <script src="/cost/js/tools/multiproduct/calcMultiproducts.js"></script>
                                <script src="/cost/js/tools/multiproduct/saveMultiproducts.js"></script>
                                <script src="/global/js/global/printPdf.js"></script>
                                <?php if ($_SESSION['status_historical'] == 1 && $_SESSION['historical'] == 1 && $_SESSION['plan_cost_historical'] == 1) { ?>
                                    <script>
                                        d_historical = "<?= $_SESSION['d_historical'] ?>";
                                        date_product = "<?= $_SESSION['date_product'] ?>";
                                    </script>
                                    <script src="/global/js/global/saveHistorical.js"></script>
                                <?php $_SESSION['status_historical'] = 2;
                                }  ?>
                                <?php
                                $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';

                                $dominio = $_SERVER['SERVER_NAME'];

                                $ruta = $_SERVER['REQUEST_URI'];

                                $url = $protocolo . $dominio . $ruta;
                                if (
                                    str_contains($url, 'demo.') && $_SESSION['demo'] == 1 &&
                                    $_SESSION['name'] == '' && $_SESSION['lastname'] == ''
                                ) {
                                ?>
                                    <script src="/global/js/global/firstLogin.js"></script>
                                <?php $_SESSION['demo'] = 2;
                                }
                                ?>

                            </div>
</body>

</html>