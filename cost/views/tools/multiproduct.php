<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php require_once dirname(dirname(__DIR__)) . '/modals/importMultiproducts.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
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
                                <div class="col-xs-2 py-2">
                                    <button class="btn btn-info" id="btnImportNewMultiproducts">Importar Multiproductos</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportMultiproducts">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportMultiproducts" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class=" form-control" type="file" id="fileMultiproducts" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Multiproductos</label>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportMultiproducts">Importar</button>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadMultiproducts">Descarga Multiproductos</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45 ">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row disable-select">
                            <div class="col-12 cardTblBreakeven">
                                <div class="card">
                                    <div class="card-header row">
                                        <h5 class="col-sm-10 card-title">Punto De Equilibrio</h5>
                                        <div class="col-sm-2 floating-label enable-floating-label show-label form-inline justify-content-sm-end cardExpenseAssignation" style="display: none;">
                                            <input type="number" class="form-control text-center" id="expenseAssignation">
                                            <label for="">Asignar Gastos</label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped text-center" id="tblBreakeven">
                                            <thead>
                                                <tr>
                                                    <th>Gastos</th>
                                                    <th>Total Unidades</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td id="expenses"></td>
                                                    <td id="totalUnits"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 cardTblMultiproducts">
                                <div class="card">
                                    <div class="card-header row">
                                        <h5 class="col-sm-10 card-title">Multiproductos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center" id="tblMultiproducts">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 150px">Referencia</th>
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
                                                        <td></td>
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

                            <div class="col-lg-12 cardGraphicMultiproducts" style="display:none;">
                                <div class="card">
                                    <div class="card-header dflex-between-center">
                                        <h5 class="card-title">Diagrama de Barras</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <canvas id="chartMultiproductsBar" style="width: 80%;"></canvas>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-header dflex-between-center">
                                            <h5 class="card-title">Diagrama de Donut</h5>
                                        </div>
                                        <div class="card-body pt-2">
                                            <div class="chart-container">
                                                <canvas id="chartMultiproductsDonut" style="width: 80%;"></canvas>
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
        <!-- Main content end -->

        <!-- Footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        flag_type_price = "<?= $_SESSION['flag_type_price'] ?>";
        DatatableTblMultiproducts = 1;
    </script>
    <script src="/cost/js/tools/multiproduct/tblMultiproducts.js"></script>
    <script src="/cost/js/tools/multiproduct/calcMultiproducts.js"></script>
    <script src="/cost/js/tools/multiproduct/saveMultiproducts.js"></script>
    <script src="/cost/js/tools/multiproduct/graphicMultiproducts.js"></script>
    <script src="/global/js/import/file.js"></script>
    <script src="/global/js/import/import.js"></script>
    <script src="/cost/js/tools/multiproduct/importMultiproducts.js"></script>
    <script src="/cost/js/tools/economyScale/configTypePrices.js"></script>
    <script>
        $(document).ready(function() {
            checkFlagPrice(2);
        });
    </script>
</body>

</html>