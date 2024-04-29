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
    <title>Tezlik - Cost | Sale-Objectives</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-graph-up mr-1"></i>Objetivos de Ventas</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Cantidades de producto minimas a vender para cumplir con el margen requerido</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label cardBottons">
                                    <input type="number" class="form-control text-center" id="profitability">
                                    <label for="profitability">Rentabilidad</label>
                                </div>
                                <div id="spinnerLoading"></div>
                            </div>
                            <!-- <div class="col-sm-4 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewEconomyScale">Nuevo Calculo</button>
                                </div>
                                <?php if ($_SESSION['price_usd'] == 1 && $_SESSION['plan_cost_price_usd'] == 1) { ?>
                                    <div class="col-xs-2">
                                        <button class="btn btn-info btnPricesUSD" id="usd">Precios USD</button>
                                    </div>
                                    <div class="col-xs-2 ml-2 form-group floating-label enable-floating-label cardUSD" style="display:none;margin-bottom:0px;">
                                        <label class="mb-1 font-weight-bold text-dark">Valor Dolar</label>
                                        <input type="text" style="background-color: aliceblue;" class="form-control text-center calcInputs" name="valueCoverage" id="valueCoverage" value="<?php
                                                                                                                                                                                            $coverage = sprintf('$ %s', number_format($_SESSION['coverage'], 2, ',', '.'));
                                                                                                                                                                                            echo  $coverage ?>" readonly>
                                    </div>
                                <?php } ?>
                            </div> -->
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
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";

        price_usd = "<?= $_SESSION['price_usd'] ?>";
        plan_cost_price_usd = "<?= $_SESSION['plan_cost_price_usd'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        flag_type_price = "<?= $_SESSION['flag_type_price'] ?>";
        coverage = "<?= $_SESSION['coverage'] ?>";
        price_usd = "<?= $_SESSION['price_usd'] ?>";
        plan_cost_price_usd = "<?= $_SESSION['plan_cost_price_usd'] ?>";

        // $(document).ready(function() {

        //     // Validar que precio estaba anteriormente seleccionado
        //     let session_flag = sessionStorage.getItem('flag_type_price');

        //     var sugeredElement = document.getElementById("sugered");
        //     var actualElement = document.getElementById("actual");

        //     // Precio Sugerido
        //     if (session_flag == '1') {
        //         $('#labelDescription').html(`Descripción (Precio Sugerido)`);

        //         sugeredElement.classList.remove("btn-outline-primary");
        //         sugeredElement.classList.add("btn-primary");

        //         actualElement.classList.remove("btn-primary");
        //         actualElement.classList.add("btn-outline-primary");
        //     } else { // Precio Actual
        //         $('#labelDescription').html(`Descripción (Precio Actual)`);

        //         sugeredElement.classList.remove("btn-primary");
        //         sugeredElement.classList.add("btn-outline-primary");

        //         actualElement.classList.remove("btn-outline-primary");
        //         actualElement.classList.add("btn-primary");
        //     }

        //     if (price_usd == '1' && plan_cost_price_usd == '1') {
        //         // Validar que valor de precio esta seleccionado
        //         let typePrice = sessionStorage.getItem('typePrice');

        //         let element = document.getElementsByClassName('btnPricesUSD')[0];

        //         // Dolares
        //         if (typePrice == '1' || !typePrice) {
        //             element.id = 'usd';
        //             element.innerText = 'Precios USD';

        //             $('.cardUSD').hide(800);
        //         } else { // Pesos
        //             element.id = 'cop';
        //             element.innerText = 'Precios COP';
        //             $('.cardUSD').show(800);
        //         }
        //     }
        // });
    </script>

    <script src="/cost/js/tools/saleObjectives/saleObjectives.js"></script>
    <script src="/cost/js/tools/saleObjectives/tblSaleObjectives.js"></script>
    <script src="/global/js/global/orderData.js"></script>
</body>

</html>