<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>

<?php require_once dirname(dirname(__DIR__)) . '/modals/modifyRecoverExpenses.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Expenses Distribution</title>
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
                <!-- Page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Gastos Generales</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active mt-2" id="descrExpense"></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2 cardBtnUpdateExpenses" style="display:none">
                                    <button class="btn btn-secondary" id="btnUpdateExpenses">Modificar Gastos</button>
                                </div>
                                <div class="col-xs-2 mr-2 cardBtnExpensesDistribution" style="display:none">
                                    <button class="btn btn-warning" id="btnExpensesDistribution">Distribuir Gastos</button>
                                </div>
                                <div class="col-xs-2 mr-2 cardBtnExpenseRecover" style="display:none">
                                    <button class="btn btn-warning" id="btnNewExpenseRecover">Recuperar Gastos</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2 cardBtnImportExpenses" style="display:none">
                                    <button id="btnImportNewExpenses" class="btn btn-primary"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardExpensesDistribution">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="col-sm-3">
                                                <label for="">Gastos a distribuir</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control number text-center" id="expensesToDistribution" name="assignableExpense" style="width: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribuir Gastos -->
                <div class="page-content-wrapper mt--45 mb-5 cardExpensesDistribution">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formExpensesDistribution">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                    <select class="form-control refProduct" name="refProduct" id="EDRefProduct"></select>
                                                    <label for="EDRefProduct">Referencia</label>
                                                </div>
                                                <div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                    <select class="form-control selectNameProduct" name="selectNameProduct" id="EDNameProduct"></select>
                                                    <label for="EDNameProduct">Nombre Producto</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                    <input type="text" class="form-control number text-center" id="undVendidas" name="unitsSold">
                                                    <label for="undVendidas">Und Vendidas</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px;margin-top:7px">
                                                    <input type="text" class="form-control number text-center" id="volVendidas" name="turnover">
                                                    <label for="volVendidas">Vol Ventas</label>
                                                </div>
                                                <div class="col-xs-2" style="margin-top:12px">
                                                    <button class="btn btn-primary" id="btnAssignExpenses">Asignar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recuperar Gastos -->
                <div class="page-content-wrapper mt--45 mb-5 cardExpenseRecover">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formExpenseRecover">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                    <select class="form-control refProduct" name="idProduct" id="ERRefProduct"></select>
                                                    <label for="ERRefProduct">Referencia</label>
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                    <select class="form-control selectNameProduct" name="nameProduct" id="ERNameProduct"></select>
                                                    <label for="ERNameProduct">Nombre Producto</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px;margin-top:7px">
                                                    <input type="number" class="form-control text-center" id="percentage" name="percentage">
                                                    <label for="percentage">Porcentaje</label>
                                                </div>
                                                <div class="col-xs-2" style="margin-top:12px">
                                                    <button class="btn btn-primary" id="btnExpenseRecover">Guardar Gasto</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportExpenses">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formImportExpenses" enctype="multipart/form-data">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                    <input class="form-control" type="file" id="fileExpenses" accept=".xls,.xlsx">
                                                    <label for="formFile" id="lblImportExpense" class="form-label"></label>
                                                </div>
                                                <div class="col-xs-2" style="margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportExpenses">Importar</button>
                                                </div>
                                                <div class="col-xs-2" style="margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsExpenses">Descarga Formato</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblExpenses">

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
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/cost/js/general/expensesDistribution/tblExpensesDistribution.js"></script>
    <script src="/cost/js/general/expenseRecover/tblExpenseRecover.js"></script>
    <script src="/cost/js/general/expenseRecover/expenseRecover.js"></script>
    <script src="/cost/js/general/expenseRecover/updateExpenses.js"></script>
    <script src="/global/js/global/orderData.js"></script>
    <!-- <script src="/cost/js/general/expenseRecover/configProducts.js"></script> -->
    <script src="/cost/js/basic/products/configProducts.js"></script>
    <script src="/cost/js/general/expensesDistribution/configExpensesDistribution.js"></script>
    <script src="/cost/js/general/expensesDistribution/expensesDistribution.js"></script>
    <script src="/global/js/import/import.js"></script>
    <script src="/cost/js/general/expensesDistribution/importExpensesDistribution.js"></script>
    <script src="/global/js/import/file.js"></script>
</body>

</html>