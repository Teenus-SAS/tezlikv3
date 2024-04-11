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
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Prices</title>
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
            <!-- Loader -->
            <div class="loading">
                <div class="loader"></div>
            </div>

            <!-- Content -->
            <div class="page-content">
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
                <!-- Page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="tab-pane cardPricesCOP">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-cash mr-1"></i>Precios de Venta</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Análisis de Precios</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-xl-6 form-inline justify-content-sm-end">
                                    <?php if ($_SESSION['flag_composite_product'] == 1) { ?>
                                        <div class="col-xs-2 mr-2">
                                            <button class="btn btn-warning" id="btnComposite">Productos Compuestos</button>
                                        </div>
                                    <?php } ?>
                                    <?php if ($_SESSION['price_usd'] == 1 && $_SESSION['plan_cost_price_usd'] == 1) { ?>
                                        <div class="col-xs-2">
                                            <button class="btn btn-info btnPricesUSD" id="usd">Precios USD</button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane cardPricesUSD" style="display: none;">
                            <div class="row align-items-center">
                                <div class="col-sm-4 col-xl-4">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark">Precios Venta USD</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">La cobertura cambiaria lo protege contra el riesgo de las fluctuaciónes del tipo de cambio.</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-xl-8 form-inline justify-content-sm-end" id="USDHeader">
                                    <div class="col-xs-2 mr-2 USDInputs">
                                        <button class="btn btn-warning" id="btnSimulation">Simular</button>
                                    </div>
                                    <div class="col-xs-2 mr-2 USDInputs">
                                        <button class="btn btn-info btnPricesUSD" id="cop">Precios COP</button>
                                    </div>
                                    <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs" style="margin-bottom: 0px;">
                                        <label class="mb-1 font-weight-bold text-dark">Valor Dolar</label>
                                        <input type="number" class="form-control text-center calcInputs" name="valueCoverage" id="valueCoverage" value="<?php
                                                                                                                                                        $coverage = sprintf("%.2f", $_SESSION['coverage']);
                                                                                                                                                        echo  $coverage ?>">
                                    </div>


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
                                <div class="card disable-select">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblPrices">

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
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        flag_composite_product = "<?= $_SESSION['flag_composite_product'] ?>";
        coverage = "<?= $_SESSION['coverage'] ?>";
        coverage1 = "<?= $_SESSION['coverage'] ?>";
        deviation = "<?= $_SESSION['deviation'] ?>";
        viewPrices = 1;
    </script>
    <script src="/global/js/global/orderData.js"></script>
    <script src="/cost/js/prices/pricesCOP/configPrices.js"></script>
    <script src="/cost/js/prices/pricesCOP/tblprices.js"></script>
    <script src="/cost/js/dashboard/calcDataCost.js"></script>

    <?php if ($_SESSION['price_usd'] == 1 && $_SESSION['plan_cost_price_usd'] == 1) { ?>
        <!-- <script src="/cost/js/prices/pricesUSD/tblPricesUSD.js"></script> -->
        <script src="/cost/js/prices/pricesUSD/pricesUSD.js"></script>
    <?php } ?>
</body>

</html>