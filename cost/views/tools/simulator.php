<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php require_once dirname(dirname(__DIR__)) . '/modals/modalSimulator.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Simulator</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
    <!-- Modal Rigth side -->
    <style type="text/css">
        @media (min-width: 1281px) {
            .come-from-modal.right .modal-dialog {
                position: fixed;
                margin: auto;
                width: 800px;
                height: 100%;
                -webkit-transform: translate3d(0%, 0, 0);
                -ms-transform: translate3d(0%, 0, 0);
                -o-transform: translate3d(0%, 0, 0);
                transform: translate3d(0%, 0, 0);
            }

            .come-from-modal.right .modal-content {
                height: 100%;
                overflow-y: auto;
                border-radius: 0px;
            }

            .come-from-modal.right .modal-body {
                padding: 15px 15px 80px;
            }

            .come-from-modal.right.fade .modal-dialog {
                right: 0;
                -webkit-transition: opacity 0.3s linear, right 0.3s ease-out;
                -moz-transition: opacity 0.3s linear, right 0.3s ease-out;
                -o-transition: opacity 0.3s linear, right 0.3s ease-out;
                transition: opacity 0.3s linear, right 0.3s ease-out;
            }

            .come-from-modal.right.fade.in .modal-dialog {
                right: 0;
            }
        }
    </style>
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
                            <div class="col-12">
                                <div class="page-title">
                                    <div class="row">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <h3 class="mb-1 font-weight-bold text-dark" id="nameProduct"></h3>
                                        </div>
                                        <div class="col-sm-2 imageProduct">
                                        </div>
                                        <div class="col-sm-4 d-flex align-items-center">
                                            <select id="product" class="form-control selectNameProduct"></select>
                                        </div>

                                        <div class="col-sm-12 mb-3 form-inline justify-content-sm-end" id="cardHeader">
                                            <div class="col-xs-2 mr-2 cardBottons">
                                                <button type="button" class="btn btn-secondary" id="btnSimulate">Simular</button>
                                            </div>
                                            <div class="col-xs-2 py-2 mr-2 cardBottons cardAddSimulator" style="display: none;">
                                                <button type="button" class="btn btn-primary" id="btnAddSimulator">Guardar Simulacion</button>
                                            </div>
                                        </div>
                                    </div>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Análisis de Costos</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid form-row">
                        <div class="col-sm-6">
                            <!-- Row 2-->
                            <div class="row align-items-stretch">
                                <div class="col-sm-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">Costeo Total - Actual</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item py-4">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <div style="display: grid;grid-template-columns:1fr 110px">
                                                                <p class="mb-2" style="color:green">Precio de Venta</p>
                                                                <h6 class="mb-0 pl-3 text-right salesPrice"></h6>
                                                                <p class="mb-2" style="color:darkcyan">Total Costos</p>
                                                                <h6 class="mb-0 pl-3 text-right costTotal"></h6>
                                                                <p class="mb-2" style="color:darkcyan">Costos</p>
                                                                <h6 class="mb-0 pl-3 text-right cost"></h6>
                                                                <p class="text-muted mb-2 pl-3">Materia Prima</p>
                                                                <h6 class="mb-0 pl-3 text-right payRawMaterial"></h6>
                                                                <p class="text-muted mb-2 pl-3">Mano de Obra</p>
                                                                <h6 class="mb-0 pl-3 text-right payWorkforce">$</h6>
                                                                <p class="text-muted mb-2 pl-3">Costos Indirectos</p>
                                                                <h6 class="mb-0 pl-3 text-right payIndirectCost">$</h6>
                                                                <p class="text-muted mb-2 pl-3">Servicios Externos</p>
                                                                <h6 class="mb-0 pl-3 text-right services">$</h6>
                                                                <p class="mb-2 expenses" style="color:darkcyan">Gastos</p>
                                                                <h6 class="mb-0 pl-3 text-right payAssignableExpenses"></h6>
                                                                <p class="mb-2 commission" style="color:darkcyan">Comisión Vta</p>
                                                                <h6 class="mb-0 pl-3 text-right commisionSale"></h6>
                                                                <p class="mb-2 profit" style="color:darkcyan">Rentabilidad</p>
                                                                <h6 class="mb-0 pl-3 text-right profitability"></h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Materia Prima</span>
                                                        <p class="mb-0 mt-1"></p>
                                                        <span class="text-info font-weight-bold">
                                                            <i class="percentRawMaterial" style="font-style: initial; font-size:18px;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Mano de Obra</span>
                                                        <p class="mb-0 mt-1"></p>
                                                        <span class="text-info font-weight-bold">
                                                            <i class="percentWorkforce" style="font-style: initial; font-size:18px;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Costos Indirectos</span>
                                                        <p class="mb-0 mt-1"></p>
                                                        <span class="text-info font-weight-bold">
                                                            <i class="percentIndirectCost" style="font-style: initial; font-size:18px;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Gastos</span>
                                                        <p class="mb-0 mt-1"></p>
                                                        <span class="text-info font-weight-bold">
                                                            <i class="percentAssignableExpenses" style="font-style: initial; font-size:18px;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <!-- Row 2-->
                            <div class="row align-items-stretch">
                                <div class="col-sm-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">Costeo Total - Simulación</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item py-4">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <div style="display: grid;grid-template-columns:1fr 110px">
                                                                <p class="mb-2" style="color:green">Precio de Venta</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 salesPrice" id="salesPrice-2"></h6>
                                                                <p class="mb-2" style="color:darkcyan">Total Costos</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 costTotal" id="costTotal-2"></h6>
                                                                <p class="mb-2" style="color:darkcyan">Costos</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 cost" id="cost-2"></h6>
                                                                <p class="text-muted mb-2 pl-3">Materia Prima</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 payRawMaterial" id="payRawMaterial-2"></h6>
                                                                <p class="text-muted mb-2 pl-3">Mano de Obra</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 payWorkforce" id="payWorkforce-2">$</h6>
                                                                <p class="text-muted mb-2 pl-3">Costos Indirectos</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 payIndirectCost" id="payIndirectCost-2">$</h6>
                                                                <p class="text-muted mb-2 pl-3">Servicios Externos</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 services" id="services-2">$</h6>
                                                                <p class="mb-2 expenses" style="color:darkcyan">Gastos</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 payAssignableExpenses" id="payAssignableExpenses-2"></h6>
                                                                <p class="mb-2 commission" style="color:darkcyan" id="commission-2">Comisión Vta</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 commisionSale" id="commisionSale-2"></h6>
                                                                <p class="mb-2 profit" style="color:darkcyan" id="profit-2">Rentabilidad</p>
                                                                <h6 class="mb-0 pl-3 text-right sim-2 profitability" id="profitability-2"></h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Materia Prima</span>
                                                        <p class="mb-0 mt-1"></p>
                                                        <span class="text-info font-weight-bold">
                                                            <i class="sim-2 percentRawMaterial" id="percentRawMaterial-2" style="font-style: initial; font-size:18px;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Mano de Obra</span>
                                                        <p class="mb-0 mt-1"></p>
                                                        <span class="text-info font-weight-bold">
                                                            <i class="sim-2 percentWorkforce" id="percentWorkforce-2" style="font-style: initial; font-size:18px;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Costos Indirectos</span>
                                                        <p class="mb-0 mt-1"></p>
                                                        <span class="text-info font-weight-bold">
                                                            <i class="sim-2 percentIndirectCost" id="percentIndirectCost-2" style="font-style: initial; font-size:18px"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Gastos</span>
                                                        <p class="mb-0 mt-1"></p>
                                                        <span class="text-info font-weight-bold">
                                                            <i class="sim-2 percentAssignableExpenses" id="percentAssignableExpenses-2" style="font-style: initial; font-size:18px;"></i>
                                                        </span>
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
        </div>
    </div>
    <!-- Page End -->
    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>

    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";

        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_composite_product = "<?= $_SESSION['flag_composite_product'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        sessionStorage.removeItem('typePrice');
    </script>
    <script src="/global/js/global/orderData.js"></script>
    <script src="/cost/js/basic/products/configProducts.js"></script>
    <script>
        $(document).ready(function() {
            getDataProducts('/api/products');
        });
    </script>
    <script src="/cost/js/basic/machines/configMachines.js"></script>
    <script src="/cost/js/dashboard/calcDataCost.js"></script>
    <script src="/cost/js/tools/simulator/loadSimulators.js"></script>
    <script src="/cost/js/tools/simulator/generalSimulator.js"></script>
    <script src="/cost/js/tools/simulator/tblSimulator.js"></script>
    <script src="/cost/js/tools/simulator/saveSimulator.js"></script>
    <script src="/global/js/general/calcPayroll.js"></script>
    <script src="/admin/js/risks/configRisks.js"></script>
    <script src="/admin/js/benefits/configBenefits.js"></script>
    <script src="/global/js/global/convertUnits.js"></script>
</body>

</html>