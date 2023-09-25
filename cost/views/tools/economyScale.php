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
    <title>Tezlik - Cost | economyScale</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Economia de Escalas</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Calculo de economia de escalas</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="btnNeweconomyScale">Nuevo Calculo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="col-sm-12 mb-2">
                                                <h5>Producto</h5>
                                            </div>
                                            <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                <select class="form-control refProduct" name="refProduct" id="refProduct"></select>
                                                <label for="refProduct" class="form-label">Referencia <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                            <div class="col-sm-8 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                <select class="form-control selectNameProduct" name="selectNameProduct" id="selectNameProduct"></select>
                                                <label for="selectNameProduct" class="form-label">Producto <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="labelDescription"></h5>
                                        <div class="table-responsive disable-select">
                                            <table id="tblEconomyScale" class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="font-weight-bold">Ventas Mensuales (Unidad)</td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="text" id="unity-0" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="text" id="unity-1">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="text" id="unity-2">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="text" id="unity-3">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="text" id="unity-4">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="text" id="unity-5">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Precio</td>
                                                        <td>
                                                            <input class="form-control text-center price general" type="text" id="price-0" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form-control number text-center price totalRevenue general" type="text" id="price-1">
                                                        </td>
                                                        <td>
                                                            <input class="form-control number text-center price totalRevenue general" type="text" id="price-2">
                                                        </td>
                                                        <td>
                                                            <input class="form-control number text-center price totalRevenue general" type="text" id="price-3">
                                                        </td>
                                                        <td>
                                                            <input class="form-control number text-center price totalRevenue general" type="text" id="price-4">
                                                        </td>
                                                        <td>
                                                            <input class="form-control number text-center price totalRevenue general" type="text" id="price-5">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold" style="color: black;">Total Ingresos</td>
                                                        <td class="font-weight-bold text-center general" id="totalRevenue-0" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="totalRevenue-1" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="totalRevenue-2" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="totalRevenue-3" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="totalRevenue-4" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="totalRevenue-5" style="color: black;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Costos Fijos</td>
                                                        <td class="text-center fixedCosts general" id="fixedCosts-0"></td>
                                                        <td class="text-center fixedCosts general" id="fixedCosts-1"></td>
                                                        <td class="text-center fixedCosts general" id="fixedCosts-2"></td>
                                                        <td class="text-center fixedCosts general" id="fixedCosts-3"></td>
                                                        <td class="text-center fixedCosts general" id="fixedCosts-4"></td>
                                                        <td class="text-center fixedCosts general" id="fixedCosts-5"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Costos Variables</td>
                                                        <td class="text-center general" id="variableCosts-0"></td>
                                                        <td class="text-center general" id="variableCosts-1"></td>
                                                        <td class="text-center general" id="variableCosts-2"></td>
                                                        <td class="text-center general" id="variableCosts-3"></td>
                                                        <td class="text-center general" id="variableCosts-4"></td>
                                                        <td class="text-center general" id="variableCosts-5"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold" style="color: black;">Total Costos y Gastos</td>
                                                        <td class="font-weight-bold text-center totalCostsAndExpenses general" style="color: black;" id="totalCostsAndExpenses-0"></td>
                                                        <td class="font-weight-bold text-center totalCostsAndExpenses general" style="color: black;" id="totalCostsAndExpenses-1"></td>
                                                        <td class="font-weight-bold text-center totalCostsAndExpenses general" style="color: black;" id="totalCostsAndExpenses-2"></td>
                                                        <td class="font-weight-bold text-center totalCostsAndExpenses general" style="color: black;" id="totalCostsAndExpenses-3"></td>
                                                        <td class="font-weight-bold text-center totalCostsAndExpenses general" style="color: black;" id="totalCostsAndExpenses-4"></td>
                                                        <td class="font-weight-bold text-center totalCostsAndExpenses general" style="color: black;" id="totalCostsAndExpenses-5"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Costo Por Unidad</td>
                                                        <td class="text-center general" id="unityCost-0"></td>
                                                        <td class="text-center unityCost general" id="unityCost-1"></td>
                                                        <td class="text-center unityCost general" id="unityCost-2"></td>
                                                        <td class="text-center unityCost general" id="unityCost-3"></td>
                                                        <td class="text-center unityCost general" id="unityCost-4"></td>
                                                        <td class="text-center unityCost general" id="unityCost-5"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Utilidad Por Unidad</td>
                                                        <td class="text-center general" id="unitUtility-0"></td>
                                                        <td class="text-center general" id="unitUtility-1"></td>
                                                        <td class="text-center general" id="unitUtility-2"></td>
                                                        <td class="text-center general" id="unitUtility-3"></td>
                                                        <td class="text-center general" id="unitUtility-4"></td>
                                                        <td class="text-center general" id="unitUtility-5"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Utilidad Neta</td>
                                                        <td class="text-center general" id="netUtility-0"></td>
                                                        <td class="text-center general" id="netUtility-1"></td>
                                                        <td class="text-center general" id="netUtility-2"></td>
                                                        <td class="text-center general" id="netUtility-3"></td>
                                                        <td class="text-center general" id="netUtility-4"></td>
                                                        <td class="text-center general" id="netUtility-5"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold" style="color: black;">Margen de Utilidad</td>
                                                        <td class="font-weight-bold text-center general" id="percentage-0" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="percentage-1" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="percentage-2" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="percentage-3" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="percentage-4" style="color: black;"></td>
                                                        <td class="font-weight-bold text-center general" id="percentage-5" style="color: black;"></td>
                                                    </tr>
                                                </tbody>
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
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        flag_type_price = "<?= $_SESSION['flag_type_price'] ?>";
    </script>
    <script src="/cost/js/tools/economyScale/configTypePrices.js"></script>
    <script>
        $(document).ready(function() {
            checkFlagPrice(1);
        });
    </script>
    <script src="/cost/js/basic/products/configProducts.js"></script>
    <script src="/global/js/global/orderData.js"></script>
    <script src="/cost/js/tools/economyScale/economyScale.js"></script>
    <script src="/cost/js/tools/economyScale/calcEconomySale.js"></script>
</body>

</html>