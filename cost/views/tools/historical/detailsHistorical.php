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
    <meta name="description" content="Cotice en minutos, y no vuelva a perder mas oportunidades de negocio">
    <meta name="keywords" content="cotizar, costos, precio, competitividad, ventajas, beneficios, diferenciacion">
    <meta name="author" content="Teenus">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Details Prices</title>
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
            <div class="page-content" id="invoice">
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
                            <div class="col-12">
                                <div class="page-title">
                                    <div class="row">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <h3 class="mb-1 font-weight-bold text-dark" id="nameProduct"></h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <ol class="col-sm-5 col-xl-6 breadcrumb mb-3 mb-md-0 cardHeader">
                                            <li class="breadcrumb-item active">Análisis de Costos</li>
                                        </ol>
                                        <!-- <div class="col-sm-5 col-xl-6 d-flex justify-content-end btnPrintPDF">
                                            <a href="javascript:;" <i class="bi bi-filetype-pdf" data-toggle='tooltip' onclick="printPDF(2)" style="font-size: 30px; color:red;"></i></a>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Widget  -->
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
                            <div class="col">
                                <div class="card radius-10 border-start border-0 border-3 border-info">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Materia Prima</span>
                                                <h2 class="mb-0 mt-1 costProduct text-info" id="rawMaterial"></h2>
                                            </div>
                                            <div class="text-center">
                                                <!-- <div id="t-rev"></div> -->
                                                <span class="text-info font-weight-bold" style="font-size:large">
                                                    <i class="" id="percentRawMaterial" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card radius-10 border-start border-0 border-3 border-info">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Mano de Obra</span>
                                                <h2 class="mb-0 mt-1 text-info" id="workforce"></h2>
                                            </div>
                                            <div class="text-center">
                                                <!-- <div id="t-order"></div> -->
                                                <span class="text-info font-weight-bold" style="font-size:large">
                                                    <i class="" id="percentWorkforce" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card radius-10 border-start border-0 border-3 border-info">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Costos Indirectos</span>
                                                <h2 class="mb-0 mt-1 number text-info" id="indirectCost"></h2>
                                            </div>
                                            <div class="text-center">
                                                <!-- <div id="t-user"></div> -->
                                                <span class="text-info font-weight-bold" style="font-size:large">
                                                    <i class="" id="percentIndirectCost" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card card card radius-10 border-start border-0 border-3 border-info">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Gastos Generales</span>
                                                <h2 class="mb-0 mt-1 number text-info" id="assignableExpenses"></h2>
                                            </div>
                                            <div class="text-center">
                                                <!-- <div id="t-visitor"></div> -->
                                                <span class="text-info font-weight-bold" style="font-size:large">
                                                    <i class="" id="percentAssignableExpenses" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 actualSalePrice">
                            <div class="col-xl-6">
                                <div class="card radius-10 border-start border-0 border-3 border-success">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="card-body row row-cols-1 row-cols-md-2 row-cols-xl-2" style="padding-bottom: 0px;padding-top: 0px">
                                                <div class="media-body align-items-center">
                                                    <span class="text-muted text-uppercase font-size-12 font-weight-bold">Precio (Lista)</span>
                                                    <h2 class="mb-0 mt-1 text-success" id="actualSalePrice" style="font-size: x-large"></h2>
                                                </div>
                                                <div class="media-body align-items-center">
                                                    <span class="text-muted text-uppercase font-size-12 font-weight-bold">Precio (Sugerido)</span>
                                                    <h3 class="mb-0 mt-1 text-warning suggestedPrice" style="font-size: x-large"></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 cardTrafficLight">
                                <!-- <div class="card radius-10 border-start border-0 border-3 border-success">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                                                <h2 class="mb-0 mt-1 costProduct text-success" id="actualProfitability"></h2>
                                            </div>
                                            <div class="text-center"> 
                                                <span class="text-success font-weight-bold" style="font-size:large">
                                                    <i class="" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                            <div class="col-xl-3">
                                <div class="card radius-10 border-start border-0 border-3 border-info">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Mínima Deseada</span>
                                                <h2 class="mb-0 mt-1 text-info" id="minProfit"></h2>
                                            </div>
                                            <div class="text-center">
                                                <!-- <div id="t-order"></div> -->
                                                <span class="text-info font-weight-bold" style="font-size:large">
                                                    <i class="" id="minProfit" style="font-style: initial;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Row 2-->
                        <div class="row align-items-stretch">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Costeo Total</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <div style="display: grid;grid-template-columns:1fr 110px ">
                                                            <p class="mb-2 salesPrice" style="color:green">Precio de Venta</p>
                                                            <h6 class="mb-0 pl-3 text-right salesPrice suggestedPrice" id="salesPrice"></h6>
                                                            <p class="mb-2 costTotal" style="color:darkcyan">Total Costos</p>
                                                            <h6 class="mb-0 pl-3 text-right costTotal" id="costTotal"></h6>
                                                            <p class="mb-2 cost" style="color:darkcyan">Costos</p>
                                                            <h6 class="mb-0 pl-3 text-right cost" id="cost"></h6>
                                                            <p class="text-muted mb-2 pl-3 payRawMaterial">Materia Prima</p>
                                                            <h6 class="mb-0 pl-3 text-right payRawMaterial" id="payRawMaterial"></h6>
                                                            <p class="text-muted mb-2 pl-3 payWorkforce">Mano de Obra</p>
                                                            <h6 class="mb-0 pl-3 text-right payWorkforce" id="payWorkforce">$</h6>
                                                            <p class="text-muted mb-2 pl-3 payIndirectCost">Costos Indirectos</p>
                                                            <h6 class="mb-0 pl-3 text-right payIndirectCost" id="payIndirectCost">$</h6>
                                                            <p class="text-muted mb-2 pl-3 services">Servicios Externos</p>
                                                            <h6 class="mb-0 pl-3 text-right services" id="services">$</h6>
                                                            <p class="mb-2 expenses" style="color:darkcyan" id="expenses">Gastos</p>
                                                            <h6 class="mb-0 pl-3 text-right expenses" id="payAssignableExpenses"></h6>
                                                            <p class="mb-2 commission" style="color:darkcyan" id="commission">Comisión Vta</p>
                                                            <h6 class="mb-0 pl-3 text-right commission" id="commisionSale"></h6>
                                                            <p class="mb-2 profit minProfit" style="color:darkcyan" id="profit">Rentabilidad</p>
                                                            <h6 class="mb-0 pl-3 text-right profit" id="profitability"></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Begin total revenue chart -->
                            <?php if ($_SESSION['flag_expense'] == 0) { ?>
                                <div class="col-sm-6" style="height: fit-content;">
                                <?php } ?>
                                <?php if ($_SESSION['flag_expense'] == 1) { ?>
                                    <div class="col-sm-6">
                                    <?php } ?>
                                    <?php if ($_SESSION['flag_expense'] == 2) { ?>
                                        <div class="col-sm-9">
                                        <?php } ?>
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Costos y Gastos</h5>
                                            </div>
                                            <div class="card-body pt-2">
                                                <!-- <div id="chartProductCosts"></div> -->
                                                <canvas id="chartProductCosts" style="width: 80%;"></canvas>
                                            </div>
                                        </div>
                                        </div>
                                        <!-- End total revenue chart -->
                                        <?php if ($_SESSION['flag_expense'] != 2) { ?>
                                            <div class="col-md-4 col-lg-3">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title">Ventas</h5>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item py-4">
                                                                <div class="media">
                                                                    <div class="media-body">
                                                                        <p class="text-muted mb-2">Número de Unidades</p>
                                                                        <h4 class="mb-0" id="unitsSold"></h4>
                                                                    </div>
                                                                    <div class="avatar avatar-md bg-info mr-0 align-self-center">
                                                                        <i class="bx bx-layer fs-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li class="list-group-item py-4">
                                                                <div class="media">
                                                                    <div class="media-body">
                                                                        <p class="text-muted mb-2">Ingresos</p>
                                                                        <h4 class="mb-0" id="turnover"></h4>
                                                                    </div>
                                                                    <div class="avatar avatar-md bg-primary mr-0 align-self-center">
                                                                        <i class="bx bx-bar-chart-alt fs-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li class="list-group-item py-4">
                                                                <div class="media">
                                                                    <div class="media-body">
                                                                        <p class="text-muted mb-2">Precio de Venta Recomendado</p>
                                                                        <h4 class="mb-0" id="recomendedPrice">$</h4>
                                                                    </div>
                                                                    <div class="avatar avatar-md bg-success mr-0 align-self-center">
                                                                        <i class="bx bx-chart fs-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <!-- Row 4-->
                                </div>
                        </div>
                    </div>
                </div>
                <!-- Main content end -->

                <!-- Footer -->
                <?php include_once  dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/footer.php'; ?>
            </div>
            <!-- Page End -->
        </div>
    </div>
    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/scriptsJS.php'; ?>

    <script src="/global/js/global/orderData.js"></script>
    <script src="/cost/js/dashboard/calcDataCost.js"></script>
    <script src="/cost/js/tools/historical/indicatorsHistorial.js"></script>
    <script src="/cost/js/tools/historical/graphicsHistorical.js"></script>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";

        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        sessionStorage.removeItem('typePrice');
    </script>
</body>

</html>