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
    <title>Tezlik - Cost | Prices-USD</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Begin Header -->
        <?php include_once dirname(dirname(dirname(__DIR__))) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once dirname(dirname(dirname(__DIR__))) . '/partials/nav.php'; ?>

        <!-- Begin main content -->
        <div class="main-content">
            <!-- Content -->
            <div class="page-content">
                <!-- Page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-xl-4">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Precios USD</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">La cobertura cambiaria es una estrategia que consiste en protegerse contra el riesgo de las fluctuaciónes del tipo de cambio entre dos monedas.</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-xl-8 form-inline justify-content-sm-end" id="USDHeader">
                                <div class="col-xs-2 mr-2 USDInputs">
                                    <label class="mb-1 font-weight-bold text-dark">Dolar Hoy</label>
                                    <input type="text" class="form-control text-center" name="valueDollar" id="valueDollar" readonly>
                                </div>
                                <div class="col-xs-2 py-2 mr-2 USDInputs">
                                    <label class="mb-1 font-weight-bold text-dark">Dolar con Cobertura</label>
                                    <input type="text" class="form-control text-center" name="valueCoverage" id="valueCoverage" readonly>
                                </div>
                                <div class="col-xs-2 py-2 mr-2 USDInputs">
                                    <label class="mb-1 font-weight-bold text-dark">Cobertura Cambiaria</label>
                                    <input type="text" class="form-control text-center" name="exchangeCoverage" id="exchangeCoverage" readonly>
                                </div>
                                <div class="col-xs-2 USDInputs">
                                    <label class="mb-1 font-weight-bold text-dark">Desviación Estándar</label>
                                    <input type="text" class="form-control text-center" name="deviation" id="deviation">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Precios</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblPricesUSD">

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
        <!-- Main content end -->

        <!-- Footer -->
        <?php include_once  dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/global/js/global/orderData.js"></script>
    <script src="/cost/js/prices/pricesUSD/tblPricesUSD.js"></script>
    <script src="/cost/js/prices/pricesUSD/pricesUSD.js"></script>
</body>

</html>