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
    <title>Tezlik - Cost | Factory Load</title>
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
                <a href="javascript:;" class="close-btn"><i class="bi bi-x-circle-fill"></i></a>
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
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-gear-wide-connected mr-1"></i>Carga Fabril</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Ingrese los costos adicionales que consume sus máquinas, ejm: Mantenimiento, cambio de partes, entre otros.</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewFactoryLoad">Nueva Carga Fabril Máquina</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewFactoryLoad" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Importar Carga Fabril Máquinas"><i class="fas fa-download" style="font-size: 20px;"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardFactoryLoad">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formNewFactoryLoad">
                                            <div class="form-row">
                                                <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Máquina</label>
                                                    <select class="form-control" name="idMachine" id="machine"></select>
                                                </div>
                                                <div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Descripción Carga fabril</label>
                                                    <input class="form-control" name="descriptionFactoryLoad" id="descriptionFactoryLoad" />
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label>Costo</label>
                                                    <input class="form-control text-center" type="number" name="costFactory" id="costFactory" />
                                                </div>
                                                <div class="col-xs-2 mt-1">
                                                    <button class="btn btn-primary" id="btnCreateFactoryLoad">Asignar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportFactoryLoad">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formImportFactoryLoad" enctype="multipart/form-data">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row" id="formFactoryLoad">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                    <input class="form-control" type="file" id="fileFactoryLoad" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label"> Importar Carga Fabril Máquinas</label>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportFactoryLoad">Importar</button>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsFactoryLoad">Descarga Formato</button>
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
                                <div class="card disable-select">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblFactoryLoad">
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Total:</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
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

        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
    </script>
    <script src="/cost/js/basic/machines/configMachines.js"></script>
    <script src="/cost/js/config/factoryLoad/tblFactoryLoad.js"></script>
    <script src="/cost/js/config/factoryLoad/factoryLoad.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/cost/js/config/factoryLoad/importFactoryLoad.js"></script>
    <script src="../global/js/import/file.js"></script>
</body>

</html>