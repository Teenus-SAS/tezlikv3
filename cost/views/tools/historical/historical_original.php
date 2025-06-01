<?php require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/api/src/Auth/authMiddleware.php'; ?>
<?php require_once dirname(dirname(dirname(__DIR__))) . '/modals/manualHistorical.php'; ?>
<?php require_once dirname(dirname(dirname(__DIR__))) . '/modals/weekHistorical.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Teenus SAS">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TezlikSoftware Cost | Historical</title>
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
                <div class="page-title-box" style="padding-bottom: 45px;">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-5">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Historico de Costos y Precios</h3>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-7">
                                <div class="row">
                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-success shadow-lg">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <a href="javascript:void(0);" class="btnsProfit text-white" id="max" title="Maximize profitability" aria-label="Maximize profitability">
                                                            <span class="font-size-12 font-weight-bold">
                                                                <i class="fas fa-arrow-circle-up mr-1" id="lblMaxProfitability"></i>Maximize
                                                            </span>
                                                        </a>
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
                                                        <a href="javascript:void(0);" class="btnsProfit text-white" id="min" title="Minimize profitability" aria-label="Minimize profitability" role="button">
                                                            <span class="font-size-12 font-weight-bold">
                                                                <i class="fas fa-arrow-circle-down mr-1" id="lblMinProfitability"></i>
                                                            </span>
                                                        </a>
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
                                            <button class="btn btn-primary ml-1 shadow-lg" data-bs-toggle="tooltip" data-bs-placement="top" title="Si no ha guardado la información de costos puede hacerlo dando clik aquí" id="btnNewHistorical" name="btnNewHistorical" class="btn btn-secondary"><i class="fas fa-save"></i></button>
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
        <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/scriptsJS.php'; ?>

    <script src="/public/js/components/orderData.js"></script>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";

        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        type = 'manual';
        sessionStorage.removeItem('typePrice');
    </script>
    <script src="/cost/js/tools/historical/historicalConfig.js"></script>
    <script src="/public/js/components/saveHistorical.js"></script>
    <script src="/cost/js/tools/historical/historicalUtils.js"></script>
    <script src="/cost/js/tools/historical/historical.js"></script>
    <script src="/cost/js/tools/historical/historicalUI.js"></script>
    <script src="/cost/js/tools/historical/historicalEvents.js"></script>
    <script src="/cost/js/tools/historical/tblHistorical.js"></script>

    <!-- Script optimizado mes-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('compactHistoricalForm');
            const btnSave = document.getElementById('btnCompactSave');

            btnSave.addEventListener('click', function() {
                if (form.checkValidity()) {
                    // Lógica para consultar histórico
                    console.log('Consulta:', document.getElementById('compactDate').value);
                } else {
                    form.classList.add('was-validated');
                }
            });
        });
    </script>

    <!-- Script de validación Semana-->
    <script>
        function validateWeekInput(input) {
            // Asegura que el valor sea una semana válida que comienza en lunes
            const value = input.value;
            if (!value) return;

            // Convertir a fecha para verificar que sea lunes
            const [year, week] = value.split('-W');
            const date = getDateOfWeek(parseInt(week), parseInt(year));

            if (date.getDay() !== 1) { // 1 = Lunes
                input.setCustomValidity('Debe seleccionar una semana que comience en lunes');
            } else {
                input.setCustomValidity('');
            }
        }

        // Función auxiliar para obtener fecha del primer día de la semana ISO
        function getDateOfWeek(week, year) {
            const simple = new Date(year, 0, 1 + (week - 1) * 7);
            const dow = simple.getDay();
            const ISOweekStart = simple;
            if (dow <= 4) {
                ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
            } else {
                ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
            }
            return ISOweekStart;
        }

        /*  document.addEventListener('DOMContentLoaded', function() {
             const form = document.getElementById('weeklyForm');
             const btnSave = document.getElementById('btnSaveHistorical');

             btnSave.addEventListener('click', function() {
                 if (form.checkValidity()) {
                     const weekValue = document.getElementById('btnSaveHistorical').value;
                     const [year, week] = weekValue.split('-W');
                     console.log(`Semana seleccionada: Año ${year}, Semana ${week}`);
                     // Aquí tu lógica para guardar/procesar
                 } else {
                     form.classList.add('was-validated');
                 }
             });
         }); */
    </script>
</body>

</html>