<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
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
    <title>Tezlik - Cost | Multiproduct</title>
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
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Multiproductos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Punto de Equilibrio Multiproducto</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-primary" id="btnShowTbl">Tabla</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-secondary" id="btnShowGraphic">Grafica</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45 ">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12 cardTblMultiproducts">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Multiproductos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center" id="tblMultiproducts">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 350px">Producto</th>
                                                        <th>No Unidades Vendidas</th>
                                                        <th style="width: 150px;">Precio</th>
                                                        <th style="width: 150px;">Costo Variable</th>
                                                        <th style="width: 150px;">Participacion</th>
                                                        <th>Margen De Contribucion</th>
                                                        <th>Promedio Ponderado</th>
                                                        <th>Unidades A Vender</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tblMultiproductsBody"></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>Total:</td>
                                                        <td id="totalSoldsUnits"></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td id="totalParticipation"></td>
                                                        <td></td>
                                                        <td id="totalAverages"></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 cardGraphicMultiproducts" style="display:none;">
                                <div class="card">
                                    <div class="card-header dflex-between-center">
                                        <h5 class="card-title">Multiproductos</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <canvas id="chartMultiproducts" style="width: 80%;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    <script src="/cost/js/tools/multiproduct/tblMultiproducts.js"></script>
    <script src="/cost/js/tools/multiproduct/calcMultiproducts.js"></script>
    <script src="/cost/js/tools/multiproduct/graphicMultiProducts.js"></script>
</body>

</html>