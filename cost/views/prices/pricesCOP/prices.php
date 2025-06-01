<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/api/src/Auth/authMiddleware.php';
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
    <title>TezlikSoftware Cost | Prices</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/scriptsCSS.php'; ?>
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
                <a href="javascript:;" class="close-btn" style="display: none;"><i class="bi bi-x-circle-fill"></i></a>
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
                        <div class="tab-pane cardCurrencyCOP">
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
                                    <!-- ?php  if ($_SESSION['flag_composite_product'] == 1) { ?>
                                        <div class="col-xs-2 mr-2 cardCOP" style="display: none;">
                                            <button class="btn btn-warning" id="btnComposite">Productos Compuestos</button>
                                        </div>
                                    ?php } ?> -->
                                    <?php
                                    // $_SESSION['price_usd'] == 1 && 
                                    if ($_SESSION['flag_currency_usd'] == 1 || $_SESSION['flag_currency_eur'] == 1) { ?>
                                        <!-- <div class="col-xs-2 cardCOP">
                                            <button class="btn btn-info btnPricesUSD" id="usd">Precios USD</button>
                                        </div> -->
                                        <div class="col-xs-2 mr-2 mt-1 floating-label enable-floating-label show-label" style="margin-bottom: 0px;">
                                            <label class="text-dark">Moneda</label>
                                            <select class="form-control selectCurrency">
                                                <option disabled>Seleccionar</option>
                                                <option value="1" selected>COP</option>
                                                <?php if ($_SESSION['flag_currency_usd'] == 1) { ?>
                                                    <option value="2">USD</option>
                                                <?php } ?>
                                                <?php if ($_SESSION['flag_currency_eur'] == 1) { ?>
                                                    <option value="3">EUR</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } ?>
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-success" id="btnExportPrices" data-toggle="tooltip" title="Exportar" style="height: 39px"><i class="fas fa-file-excel fa-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane cardCurrencyUSD" style="display: none;">
                            <div class="row align-items-center">
                                <div class="col-sm-4 col-xl-4">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark">Precios Venta Dolares</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">La cobertura cambiaria lo protege contra el riesgo de las fluctuaciónes del tipo de cambio.</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-xl-8 form-inline justify-content-sm-end" id="USDHeader">

                                    <div class="col-xs-2 mr-2 USDInputs floating-label enable-floating-label show-label">
                                        <!-- <button class="btn btn-info btnPricesUSD" id="cop">Precios COP</button> -->
                                        <label class="text-dark">Moneda</label>
                                        <select class="form-control selectCurrency">
                                            <option disabled>Seleccionar</option>
                                            <option value="1">COP</option>
                                            <?php if ($_SESSION['flag_currency_usd'] == 1) { ?>
                                                <option value="2" selected>USD</option>
                                            <?php } ?>
                                            <?php if ($_SESSION['flag_currency_eur'] == 1) { ?>
                                                <option value="3">EUR</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label coverageUSDInput">
                                        <label class="mb-1 font-weight-bold text-dark">Valor Dolar</label>
                                        <input type="number" class="form-control text-center calcUSDInputs" name="valueCoverageUSD" id="valueCoverageUSD" value="<?php
                                                                                                                                                                    $coverage_usd = sprintf("%.2f", $_SESSION['coverage_usd']);
                                                                                                                                                                    echo  $coverage_usd ?>">
                                    </div>
                                    <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs coverageUSDInput">
                                        <label class="font-weight-bold text-dark">Cobertura Cambiaria</label>
                                        <input type="text" class="form-control text-center" name="exchangeCoverageUSD" id="exchangeCoverageUSD" style="background-color: aliceblue;" readonly>
                                    </div>
                                    <div class="col-xs-2 mr-2 mb-4 USDInputs">
                                        <button class="btn btn-warning" id="btnSimulation">Simular</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane cardCurrencyEUR" style="display: none;">
                            <div class="row align-items-center">
                                <div class="col-sm-4 col-xl-4">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark">Precios Venta Euros</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">La cobertura cambiaria lo protege contra el riesgo de las fluctuaciónes del tipo de cambio.</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-xl-8 form-inline justify-content-sm-end" id="EURHeader">
                                    <div class="col-xs-2 mr-2 mb-4 EURInputs">
                                        <button class="btn btn-warning">Simular</button>
                                    </div>
                                    <div class="col-xs-2 mr-2 EURInputs floating-label enable-floating-label show-label">
                                        <!-- <button class="btn btn-info btnPricesUSD" id="cop">Precios COP</button> -->
                                        <label class="text-dark">Moneda</label>
                                        <select class="form-control selectCurrency">
                                            <option disabled>Seleccionar</option>
                                            <option value="1">COP</option>
                                            <?php if ($_SESSION['flag_currency_usd'] == 1) { ?>
                                                <option value="2" selected>USD</option>
                                            <?php } ?>
                                            <?php if ($_SESSION['flag_currency_eur'] == 1) { ?>
                                                <option value="3">EUR</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label coverageEURInput">
                                        <label class="mb-1 font-weight-bold text-dark">Valor Euro</label>
                                        <input type="number" class="form-control text-center calcEURInputs" name="valueCoverageEUR" id="valueCoverageEUR" value="<?php
                                                                                                                                                                    $coverage_eur = sprintf("%.2f", $_SESSION['coverage_eur']);
                                                                                                                                                                    echo  $coverage_eur ?>">
                                    </div>
                                    <!-- <div class="col-xs-2 mt-4 mr-2 form-group floating-label enable-floating-label USDInputs coverageInput" style="margin-bottom: 0px;">
                                        <label class="font-weight-bold text-dark">Cobertura Cambiaria</label>
                                        <input type="text" class="form-control text-center" name="exchangeCoverage" id="exchangeCoverage" style="background-color: aliceblue;" readonly>
                                    </div> -->
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
        <?php include_once  dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/scriptsJS.php'; ?>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";
        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_currency_eur = "<?= $_SESSION['flag_currency_eur'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        flag_composite_product = "<?= $_SESSION['flag_composite_product'] ?>";
        coverage_usd = "<?= $_SESSION['coverage_usd'] ?>";
        coverage_usd1 = "<?= $_SESSION['coverage_usd'] ?>";
        coverage_eur = "<?= $_SESSION['coverage_eur'] ?>";
        coverage_eur1 = "<?= $_SESSION['coverage_eur'] ?>";
        deviation = "<?= $_SESSION['deviation'] ?>";
        id_company = "<?= $_SESSION['id_company'] ?>";
        viewPrices = 1;
    </script>
    <script src="/public/js/components/orderData.js"></script>
    <script src="/cost/js/prices/pricesCOP/configPrices.js"></script>
    <script src="/cost/js/prices/pricesCOP/tblprices.js"></script>
    <script src="/cost/js/dashboard/calcDataCost.js"></script>

    <?php
    // $_SESSION['price_usd'] == 1 &&
    if ($_SESSION['flag_currency_usd'] == 1) { ?>
        <script src="/cost/js/prices/pricesUSD/pricesUSD.js"></script>
    <?php } ?>
    <?php if ($_SESSION['flag_currency_eur'] == 1) { ?>
        <script src="/cost/js/prices/priceEUR/pricesEUR.js"></script>
    <?php } ?>
</body>

</html>