<?php
require_once dirname(dirname(dirname(__DIR__))) . '/api/src/Auth/authMiddleware.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Teenus SAS">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TezlikSoftware Admin | Dashboard</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />
    <?php include_once dirname(__DIR__) . '/global/partials/scriptsCSS.php'; ?>
</head>


<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Dashboard Administrador</h3>
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
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Productos</span>
                                                <h2 class="mb-0 mt-1" id="products"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-rev"></div>
                                                <span class="text-info font-weight-bold font-size-23">
                                                    <i class='bx bx-grid-alt fs-lg'></i>
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
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Empresas</span>
                                                <h2 class="mb-0 mt-1" id="companies"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-rev"></div>
                                                <span class="text-danger font-weight-bold font-size-13">
                                                    <i class='bx bxs-buildings fs-lg'></i>
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
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Usuarios</span>
                                                <h2 class="mb-0 mt-1" id="users"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-user"></div>
                                                <span class="text-info font-weight-bold font-size-13">
                                                    <i class='bx bxs-user-detail fs-lg'></i>
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
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Usuarios en Sesión</span>
                                                <h2 class="mb-0 mt-1" id="usersSession"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-visitor"></div>
                                                <span class="text-success font-weight-bold font-size-13">
                                                    <i class='bx bxs-user-plus fs-lg'></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2-->
                        <div class="row d-flex align-items-center">
                            <!-- Begin total sales chart -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Usuarios (Activos)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartActualUsers" style="width: 80%;"></canvas>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Total</p>
                                                <p class="mb-0 font-weight-bold" id="totalActualUsers"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Mes</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartMonth" style="width: 80%;"></canvas>
                                            <div class="center-text">
                                                <!-- <p class="text-muted mb-1 font-weight-600">Total</p> -->
                                                <p class="mb-0 font-weight-bold" id="totalMonth"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Año</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartYear" style="width: 80%;"></canvas>
                                            <div class="center-text">
                                                <!-- <p class="text-muted mb-1 font-weight-600">Total</p> -->
                                                <p class="mb-0 font-weight-bold" id="totalYear"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Empresas</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="chartCompanies"></canvas>
                                        <!-- <div class="chart-container">
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Total</p>
                                                <p class="mb-0 font-weight-bold" id="totalComapnies"></p>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Usuarios</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="chartUsers"></canvas>
                                        <!-- <div class="chart-container">
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Total</p>
                                                <p class="mb-0 font-weight-bold" id="totalUsers"></p>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Total Ingresos</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="chartTotalLogin"></canvas>
                                        <!-- <div class="chart-container">
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Total</p>
                                                <p class="mb-0 font-weight-bold" id="totalUsers"></p>
                                            </div>
                                        </div> -->
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
    <script src="/admin/js/global/changeCompany.js"></script>
    <script src="/admin/js/companies/configCompanies.js"></script>
    <script src="/global/js/global/actualDate.js"></script>
    <script src="/admin/js/dashboard/dashboardIndicatorsGeneral.js"></script>
    <script src="/admin/js/dashboard/graphicsGeneral.js"></script>
</body>

</html>