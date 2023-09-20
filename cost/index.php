<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php require_once __DIR__ . '/modals/modalExpensesByPuc.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <title>Tezlik | Dashboard</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(__DIR__) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Loader -->
        <div class="loading">
            <div class="loader"></div>
        </div>

        <!-- Begin Header -->
        <?php include_once (__DIR__) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once (__DIR__) . '/partials/nav.php'; ?>

        <!-- Begin main content -->
        <div class="main-content">
            <!-- content -->
            <div class="page-content">
                <!-- page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Dashboard Consolidado</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Bienvenido</li>
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
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5">

                            <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                <div class="col-xl-2"> <!-- Products -->
                                <?php } ?>
                                <?php if ($_SESSION['flag_expense'] == 2) { ?>
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
                                                        <i class='bx bxs-spreadsheet fs-xl'></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                    <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                        <div class="col-xl-2"> <!-- Materials -->
                                        <?php } ?>
                                        <?php if ($_SESSION['flag_expense'] == 2) { ?>
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
                                                                <i class='bx bxs-package fs-xl'></i>
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
                                                                    <i class='bx bx-money fs-xl'></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                                <div class="col-xl-3"> <!-- Expenses -->
                                                <?php } ?>
                                                <?php if ($_SESSION['flag_expense'] == 2) { ?>
                                                    <div class="col"> <!-- Expenses -->
                                                    <?php } ?>

                                                    <div class="card radius-10 border-start border-0 border-3 border-info">
                                                        <div class="card-body">
                                                            <div class="media align-items-center">
                                                                <div class="media-body">
                                                                    <span class="text-muted text-uppercase font-size-12 font-weight-bold" id="expenses"></span>
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
                                                    <div class="col">
                                                        <div class="card radius-10 border-start border-0 border-3 border-warning">
                                                            <div class="card-body">
                                                                <div class="media align-items-center">
                                                                    <div class="media-body">
                                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">PTO EQUILIBRIO</span>
                                                                        <h2 class="mb-0 mt-1 text-warning" id="multiproducts"></h2>
                                                                    </div>
                                                                    <div class="align-self-center mt-1">
                                                                        <i class="bx bx-scan fs-xl"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Row 2-->
                                                <div class="row align-items-stretch">
                                                    <div class="col-md-4 col-lg-3">
                                                        <div class="card bg-success">
                                                            <div class="card-body">
                                                                <div class="media text-white">
                                                                    <div class="media-body">
                                                                        <span class="text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                                                                        <h2 class="mb-0 mt-1 text-white" id="actualProfitabilityAverage"></h2>
                                                                    </div>
                                                                    <div class="align-self-center mt-1">
                                                                        <i class="bx bx-line-chart fs-xl"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card bg-warning">
                                                            <div class="card-body">
                                                                <div class="media text-white">
                                                                    <div class="media-body">
                                                                        <span class="text-uppercase font-size-12 font-weight-bold">Rentabilidad Mínima Deseada</span>
                                                                        <h2 class="mb-0 mt-1 text-white" id="profitabilityAverage"></h2>
                                                                    </div>
                                                                    <div class="align-self-center mt-1">
                                                                        <i class="bx bx-bar-chart-alt fs-xl"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Tiempos -->
                                                        <div class="card"> <!-- Tiempos -->
                                                            <!-- <div class="card-header">
                                                                    <h5 class="card-title">Tiempos Fabricación (Prom)</h5>
                                                            </div> -->
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
                                                                            <button class="btn btn-sm btn-primary" id="sugered" value="1">Sugerido</button>
                                                                            <button class="btn btn-sm btn-outline-primary" id="actual" value="2">Actual</button>
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
                                                                                            <p class="text-muted mb-2">Rentabilidad Promedio</p>
                                                                                            <h4 class="mb-0" id="profitabilityAverage"></h4>
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

                                                            <!-- </div> -->

                                                            <!-- <div class="row"> -->

                                                            <div class="col-lg-4">
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
                        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
                    </script>
                    <script src="js/dashboard/indicatorsGeneral.js"></script>
                    <script src="js/dashboard/calcDataCost.js"></script>
                    <script src="js/dashboard/graphicsGeneral.js"></script>
                    <script src="js/dashboard/generalExpenses.js"></script>
                </div>
</body>

</html>