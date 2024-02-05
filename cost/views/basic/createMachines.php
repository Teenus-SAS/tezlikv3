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
    <title>Tezlik - Cost | Machines</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Máquinas</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Máquinas</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewMachine" name="btnNewMachine">Nueva Máquina</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewMachines" name="btnNewImportMachines">Importar Máquinas</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateMachines">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateMachine">
                                            <div class="form-row">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="text" class="form-control" name="machine" id="machine">
                                                    <label for="">Nombre</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="number" class="form-control text-center" name="cost" id="costMachine" data-toggle="tooltip" title="Ingrese el precio de compra">
                                                    <label for="">Precio</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="number" class="form-control text-center" name="residualValue" id="residualValue" data-toggle="tooltip" title="Ingrese el valor que podria vender la máquina al finalizar su vida util, si aplica">
                                                    <label for="">Valor Residual</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="number" class="form-control text-center" name="depreciationYears" id="depreciationYears">
                                                    <label for="">Años Depreciación</label>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="number" class="form-control text-center" name="hoursMachine" id="hoursMachine">
                                                    <label for="">Horas de Trabajo (día)</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <input type="number" class="form-control text-center" name="daysMachine" id="daysMachine">
                                                    <label for="">Dias de Trabajo (Mes)</label>
                                                </div>

                                                <?php if ($_SESSION['inyection'] == 1) {
                                                ?>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                        <input type="number" class="form-control text-center" name="ciclesMachine" id="ciclesMachine">
                                                        <label for="">Ciclos Maquina</label>
                                                    </div>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                        <input type="number" class="form-control text-center" name="cavities" id="cavities">
                                                        <label for="">No Cavidades</label>
                                                    </div>
                                                <?php }
                                                ?>

                                                <div class="col-xs-2" style="margin-bottom:0px;margin-top:5px;">
                                                    <button class="btn btn-success" id="btnCreateMachine">Crear Máquina</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportMachines">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportMachines" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formMachines">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileMachines" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Máquinas</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportMachines">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsMachines">Descarga Formato</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card disable-select">
                                    <div class="card-header">
                                        <h5 class="card-title">Máquinas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblMachines">

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
        inyection = "<?= $_SESSION['inyection'] ?>";
    </script>
    <script src="/cost/js/basic/machines/tblMachines.js"></script>
    <script src="/cost/js/basic/machines/machines.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/cost/js/basic/machines/importMachines.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateExt.js"></script>
</body>

</html>