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
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-graph-up mr-1"></i>Economia de Escalas</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Calculo de economia de escalas</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewEconomyScale">Nuevo Calculo</button>
                                </div>
                                <div class="col-xs-2">
                                    <button class="btn btn-info btnPricesUSD" id="usd">Precios USD</button>
                                </div>
                                <div class="col-xs-2 ml-2 form-group floating-label enable-floating-label cardUSD" style="display:none;margin-bottom:0px;">
                                    <label class="mb-1 font-weight-bold text-dark">Valor Dolar</label>
                                    <input type="text" style="background-color: aliceblue;" class="form-control text-center calcInputs" name="valueCoverage" id="valueCoverage" value="<?php
                                                                                                                                                                                        $coverage = sprintf('$ %s', number_format($_SESSION['coverage'], 2, ',', '.'));
                                                                                                                                                                                        echo  $coverage ?>" readonly>
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
                                        <div class="d-flex flex-column">
                                            <div class="align-self-end btn-group">
                                                <button class="btn btn-sm btn-primary typePrice cardBottons" id="sugered" value="1">Precio (Sugerido)</button>
                                                <button class="btn btn-sm btn-outline-primary typePrice cardBottons" id="actual" value="2">Precio (Lista)</button>
                                                <div id="spinnerLoading"></div>
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
                                                            <input class="form-control text-center totalRevenue general" type="number" id="unity-1">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="number" id="unity-2">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="number" id="unity-3">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="number" id="unity-4">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center totalRevenue general" type="number" id="unity-5">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Precio</td>
                                                        <td>
                                                            <input class="form-control text-center price general" type="text" id="price-0" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center price totalRevenue general" type="number" id="price-1">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center price totalRevenue general" type="number" id="price-2">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center price totalRevenue general" type="number" id="price-3">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center price totalRevenue general" type="number" id="price-4">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-center price totalRevenue general" type="number" id="price-5">
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
        coverage = "<?= $_SESSION['coverage'] ?>";

        $(document).ready(function() {

            // Validar que precio estaba anteriormente seleccionado
            let session_flag = sessionStorage.getItem('flag_type_price');

            var sugeredElement = document.getElementById("sugered");
            var actualElement = document.getElementById("actual");

            // Precio Sugerido
            if (session_flag == '1') {
                $('#labelDescription').html(`Descripción (Precio Sugerido)`);

                sugeredElement.classList.remove("btn-outline-primary");
                sugeredElement.classList.add("btn-primary");

                actualElement.classList.remove("btn-primary");
                actualElement.classList.add("btn-outline-primary");
            } else { // Precio Actual
                $('#labelDescription').html(`Descripción (Precio Actual)`);

                sugeredElement.classList.remove("btn-primary");
                sugeredElement.classList.add("btn-outline-primary");

                actualElement.classList.remove("btn-outline-primary");
                actualElement.classList.add("btn-primary");
            }

            // Validar que valor de precio esta seleccionado
            let typePrice = sessionStorage.getItem('typePrice');

            let element = document.getElementsByClassName('btnPricesUSD')[0];

            // Dolares
            if (typePrice == '1' || !typePrice) {
                element.id = 'usd';
                element.innerText = 'Precios USD';

                $('.cardUSD').hide(800);
            } else { // Pesos
                element.id = 'cop';
                element.innerText = 'Precios COP';
                $('.cardUSD').show(800);
            }
        });
    </script>

    <script src="/cost/js/tools/economyScale/configTypePrices.js"></script>
    <script src="/cost/js/basic/products/configProducts.js"></script>
    <script src="/global/js/global/orderData.js"></script>
    <script src="/cost/js/tools/economyScale/economyScale.js"></script>
    <script src="/cost/js/tools/economyScale/calcEconomyScale.js"></script>
</body>

</html>