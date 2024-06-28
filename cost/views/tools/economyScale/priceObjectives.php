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
    <title>Tezlik - Cost | Price-Objectives</title>
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
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-graph-up mr-1"></i>Objetivos De Precios</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Cantidades de producto minimas a vender para cumplir con el margen requerido</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xl-6 form-inline justify-content-sm-end">
                                <?php if ($_SESSION['flag_currency_usd'] == 1 || $_SESSION['flag_currency_eur'] == 1) { ?>
                                    <div class="col-xs-2 mr-2 floating-label enable-floating-label show-label" style="margin-bottom: 0px;">
                                        <label class="text-dark">Moneda</label>
                                        <select class="form-control selectCurrency" id="selectCurrency">
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
                                    <div class="col-xs-2 ml-2 form-group floating-label enable-floating-label cardUSD" style="display:none;margin-bottom:0px;">
                                        <label class="mb-1 font-weight-bold text-dark">Valor Dolar</label>
                                        <input type="text" style="background-color: aliceblue;" class="form-control text-center" name="valueCoverageUSD" id="valueCoverageUSD" value="<?php
                                                                                                                                                                                        $coverage_usd = sprintf('$ %s', number_format($_SESSION['coverage_usd'], 2, ',', '.'));
                                                                                                                                                                                        echo  $coverage_usd ?>" readonly>
                                    </div>
                                    <div class="col-xs-2 ml-2 form-group floating-label enable-floating-label cardEUR" style="display: none; margin-bottom:0px;">
                                        <label class="font-weight-bold text-dark">Valor Euro</label>
                                        <input type="text" style="background-color: aliceblue;" class="form-control text-center" name="valueCoverageEUR" id="valueCoverageEUR" value="<?php
                                                                                                                                                                                        $coverage_eur = sprintf('$ %s', number_format($_SESSION['coverage_eur'], 2, ',', '.'));
                                                                                                                                                                                        echo  $coverage_eur ?>" readonly>
                                    </div>
                                <?php } ?>
                                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label cardBottons mt-4">
                                    <input type="number" class="form-control calcPrice text-center" id="profitability">
                                    <label for="profitability">Rentabilidad</label>
                                </div>
                                <div id="spinnerLoading"></div>
                            </div>
                            <div class="col-sm-4 col-xl-12 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2 cardBottons">
                                    <label class="text-dark"> Unidades</label>
                                </div>
                                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label cardBottons mt-4">
                                    <input type="number" class="form-control calcPrice text-center" id="unity-1">
                                </div>
                                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label cardBottons mt-4">
                                    <input type="number" class="form-control calcPrice text-center" id="unity-2">
                                </div>
                                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label cardBottons mt-4">
                                    <input type="number" class="form-control calcPrice text-center" id="unity-3">
                                </div>
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning cardBottons" id="calcPriceObj">Calcular</button>
                                </div>
                                <div class="col-xs-2">
                                    <button class="btn btn-success cardBottons" id="btnExportPObjectives" data-toggle="tooltip" title="" style="height: 39px" data-original-title="Exportar"><i class="fas fa-file-excel fa-lg"></i></button>
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
                                            <table class="table table-striped" id="tblProducts">
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

        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_currency_eur = "<?= $_SESSION['flag_currency_eur'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        flag_type_price = "<?= $_SESSION['flag_type_price'] ?>";
        coverage_usd = "<?= $_SESSION['coverage_usd'] ?>";
        coverage_eur = "<?= $_SESSION['coverage_eur'] ?>";
        flag_composite_product = "<?= $_SESSION['flag_composite_product'] ?>";
        anual_expense = "<?= $_SESSION['anual_expense'] ?>";
        flag_expense_anual = "<?= $_SESSION['flag_expense_anual'] ?>";

        $(document).ready(function() {
            if (flag_currency_usd == '1' || flag_currency_eur == '1') {
                // Validar que valor de precio esta seleccionado
                let typeCurrency = sessionStorage.getItem('typeCurrency');

                $('.cardUSD').hide(800);
                $('.cardEUR').hide(800);

                switch (typeCurrency) {
                    default: // Pesos COP
                        $('#selectCurrency').val('1');
                        $('.selectTypeExpense').show();
                        break;
                    case '2': // Dólares  
                        $('#selectCurrency').val('2');

                        $('.cardUSD').show(800);
                        break;
                    case '3': // Euros
                        $('#selectCurrency').val('3');
                        $('.cardEUR').show(800);
                        break;
                }
            }
        });
    </script>

    <script src="/cost/js/tools/economyScale/priceObjectives/priceObjectives.js"></script>
    <script src="/cost/js/tools/economyScale/priceObjectives/tblPriceObjectives.js"></script>
    <script src="/global/js/global/orderData.js"></script>
</body>

</html>