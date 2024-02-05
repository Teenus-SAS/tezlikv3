<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php require_once dirname(dirname(dirname(__DIR__))) . '/modals/manualHistorical.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Historical</title>
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
                            <div class="alert alert-danger" role="alert" style="margin-bottom: 0px;"> ¡Pronto se acabara tu licencia (<?php echo $_SESSION['license_days']; ?> días). Comunícate con tu administrador para mas información! </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($_SESSION['license_days'] > 30 && $_SESSION['license_days'] < 40) { ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-warning" role="alert" style="margin-bottom: 0px;"> ¡Pronto se acabara tu licencia. Comunícate con tu administrador para mas información! </div>
                        </div>
                    </div>
                <?php } ?>
                <!-- Page header -->
                <div class="page-title-box" style="padding-bottom: 45px;">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-5">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Historico de Costos y Precios</h3>
                                    <!-- <ol class="breadcrumb mb-3 mb-md-0">
										<li class="breadcrumb-item active">Si sus costos se almaceron, puede encontrarlo aquí</li>
									</ol> -->
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-7">
                                <div class="row">
                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-success shadow-lg">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <a href="javascript:;" class="btnsProfit" style="color:white;" id="max" <span class="font-size-12 font-weight-bold" style="font-size: smaller;"><i class="fas fa-arrow-circle-up mr-1" id="lblMaxProfitability"></i></span></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-danger shadow-lg">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <a href="javascript:;" class="btnsProfit" style="color:white;" id="min" <span class="font-size-12 font-weight-bold" style="font-size: smaller;"><i class="fas fa-arrow-circle-down mr-1" id="lblMinProfitability"></i></span></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-warning shadow-lg">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="font-size-12 font-weight-bold" style="font-size: smaller;"><i class="fas fa-arrow-circle-right mr-1" id="lblAverageProfitability"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45" style="margin-bottom: 60px;">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center justify-content-sm-end">
                                    <div class="col-sm-5 col-xl-5 form-inline">
                                        <div class="mb-1 mr-3 d-flex align-items-center floating-label enable-floating-label show-label">
                                            <label class="font-weight-bold text-dark">Mes</label>
                                            <select id="month" class="form-control">
                                            </select>
                                        </div>
                                        <div class="mb-1 mr-3 d-flex align-items-center floating-label enable-floating-label show-label">
                                            <label class="font-weight-bold text-dark">Año</label>
                                            <select id="year" class="form-control">
                                            </select>
                                        </div>
                                        <div class="mb-1 d-flex align-items-center floating-label enable-floating-label show-label">
                                            <button class="btn btn-warning mr-1 shadow-lg typeHistorical" data-bs-toggle="tooltip" data-bs-placement="top" title="Lista" id="btnList"><i class="fas fa-list-ul"></i></button>
                                            <button class="btn btn-success mr-1 shadow-lg typeHistorical" data-bs-toggle="tooltip" data-bs-placement="top" title="Graficos" id="btnGraphic"><i class="fas fa-chart-line"></i></button>
                                            <p style="font-size:40px; margin-bottom:0px; line-height:0px">|</p>
                                            <button class="btn btn-primary ml-1 shadow-lg" data-bs-toggle="tooltip" data-bs-placement="top" title="Si no ha guardado la información de costos puede hacerlo dando clik aquí" id="btnNewManualHistorical" name="btnNewManualHistorical" class="btn btn-secondary"><i class="fas fa-save"></i></button>
                                            <!-- <label>Tipo</label>
                                        <select id="typeHistorical" class="form-control">
                                            <option disabled selected>Seleccionar</option>
                                            <option value="1">Tabla</option>
                                            <option value="2">Grafico</option>
                                        </select> -->
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- page content -->
                <div class="page-content-wrapper mt--45 cardTblPrices">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card disable-select">
                                    <!-- <div class="card-header">
                                        <h5 class="card-title">Precios</h5>
                                    </div> -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblHistorical">

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 cardDashboard">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card disable-select">
                                    <div class="card-header">
                                        <h5 class="card-title">Dashboard</h5>
                                    </div>
                                    <div class="card-body">

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
        <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/scriptsJS.php'; ?>

    <script src="/global/js/global/orderData.js"></script>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        type = 'manual';
    </script>
    <script src="/global/js/global/saveHistorical.js"></script>
    <script src="/cost/js/tools/historical/tblHistorical.js"></script>
    <script src="/cost/js/tools/historical/historical.js"></script>
</body>

</html>